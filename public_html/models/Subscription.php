<?php
require_once 'BaseModel.php';

class Subscription extends BaseModel {
    protected $table = 'subscription';
    
    protected $fillable = [
        'name', 'nextdate', 'price', 'site', 'note', 'account'
    ];
    
    protected $hidden = [];
    
    // 獲取所有訂閱，按下次付款日期排序
    public function getAllSubscriptions($orderBy = 'nextdate ASC', $limit = null) {
        return $this->all([], $orderBy, $limit);
    }
    
    // 根據分類獲取訂閱
    public function getByCategory($category, $limit = null) {
        return $this->all(['category' => $category], 'next_payment_date ASC', $limit);
    }
    
    // 根據狀態獲取訂閱
    public function getByStatus($status, $limit = null) {
        return $this->all(['status' => $status], 'next_payment_date ASC', $limit);
    }
    
    // 搜尋訂閱
    public function searchSubscriptions($query, $limit = null) {
        return $this->search($query, ['name', 'description', 'website_url', 'notes'], [], 'next_payment_date ASC', $limit);
    }
    
    // 獲取即將到期的訂閱
    public function getExpiringSubscriptions($days = 7) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE next_payment_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND next_payment_date >= CURDATE()
                AND status = 'active'
                AND deleted_at IS NULL 
                ORDER BY next_payment_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取已過期的訂閱
    public function getExpiredSubscriptions() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE next_payment_date < CURDATE()
                AND status = 'active'
                AND deleted_at IS NULL 
                ORDER BY next_payment_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 獲取活躍訂閱
    public function getActiveSubscriptions($limit = null) {
        return $this->all(['status' => 'active'], 'next_payment_date ASC', $limit);
    }
    
    // 獲取統計信息
    public function getStatistics() {
        $total = $this->count();
        $active = $this->count(['status' => 'active']);
        
        // 3天內到期
        $expiring3Days = count($this->getExpiringSubscriptions(3));
        
        // 7天內到期
        $expiring7Days = count($this->getExpiringSubscriptions(7));
        
        // 30天內到期
        $expiring30Days = count($this->getExpiringSubscriptions(30));
        
        // 已過期
        $expired = count($this->getExpiredSubscriptions());
        
        // 按分類統計
        $sql = "SELECT category, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY category";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $categoryStats = $stmt->fetchAll();
        
        // 按狀態統計
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $statusStats = $stmt->fetchAll();
        
        // 按計費週期統計
        $sql = "SELECT billing_cycle, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY billing_cycle";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $billingStats = $stmt->fetchAll();
        
        // 月度總支出
        $sql = "SELECT SUM(
                    CASE billing_cycle
                        WHEN 'monthly' THEN price
                        WHEN 'quarterly' THEN price / 3
                        WHEN 'yearly' THEN price / 12
                        ELSE 0
                    END
                ) as monthly_cost FROM {$this->table} WHERE status = 'active' AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $monthlyCost = $stmt->fetchColumn();
        
        // 年度總支出
        $sql = "SELECT SUM(
                    CASE billing_cycle
                        WHEN 'monthly' THEN price * 12
                        WHEN 'quarterly' THEN price * 4
                        WHEN 'yearly' THEN price
                        ELSE 0
                    END
                ) as yearly_cost FROM {$this->table} WHERE status = 'active' AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $yearlyCost = $stmt->fetchColumn();
        
        return [
            'total' => $total,
            'active' => $active,
            'expiring_3_days' => $expiring3Days,
            'expiring_7_days' => $expiring7Days,
            'expiring_30_days' => $expiring30Days,
            'expired' => $expired,
            'monthly_cost' => $monthlyCost ?: 0,
            'yearly_cost' => $yearlyCost ?: 0,
            'category_stats' => $categoryStats,
            'status_stats' => $statusStats,
            'billing_stats' => $billingStats
        ];
    }
    
    // 創建訂閱記錄
    public function createSubscription($data) {
        // 處理帳戶信息
        if (isset($data['account_info']) && is_array($data['account_info'])) {
            $data['account_info'] = json_encode($data['account_info']);
        }
        
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        // 如果沒有設置下次付款日期，根據開始日期和計費週期計算
        if (!isset($data['next_payment_date']) && isset($data['start_date']) && isset($data['billing_cycle'])) {
            $data['next_payment_date'] = $this->calculateNextPaymentDate($data['start_date'], $data['billing_cycle']);
        }
        
        return $this->create($data);
    }
    
    // 更新訂閱記錄
    public function updateSubscription($id, $data) {
        // 處理帳戶信息
        if (isset($data['account_info']) && is_array($data['account_info'])) {
            $data['account_info'] = json_encode($data['account_info']);
        }
        
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        return $this->update($id, $data);
    }
    
    // 計算下次付款日期
    private function calculateNextPaymentDate($startDate, $billingCycle) {
        $date = new DateTime($startDate);
        
        switch ($billingCycle) {
            case 'monthly':
                $date->add(new DateInterval('P1M'));
                break;
            case 'quarterly':
                $date->add(new DateInterval('P3M'));
                break;
            case 'yearly':
                $date->add(new DateInterval('P1Y'));
                break;
            default:
                // one-time 不需要下次付款日期
                return null;
        }
        
        return $date->format('Y-m-d');
    }
    
    // 更新付款日期
    public function updatePaymentDate($id, $paymentDate = null) {
        if (!$paymentDate) {
            $paymentDate = date('Y-m-d');
        }
        
        $subscription = $this->find($id);
        if (!$subscription) {
            return false;
        }
        
        $nextPaymentDate = $this->calculateNextPaymentDate($paymentDate, $subscription['billing_cycle']);
        
        return $this->update($id, [
            'last_payment_date' => $paymentDate,
            'next_payment_date' => $nextPaymentDate
        ]);
    }
    
    // 獲取需要提醒的訂閱
    public function getSubscriptionsNeedingReminder() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE next_payment_date <= DATE_ADD(CURDATE(), INTERVAL reminder_days DAY)
                AND next_payment_date >= CURDATE()
                AND status = 'active'
                AND deleted_at IS NULL 
                ORDER BY next_payment_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 暫停訂閱
    public function pauseSubscription($id) {
        return $this->update($id, ['status' => 'paused']);
    }
    
    // 恢復訂閱
    public function resumeSubscription($id) {
        return $this->update($id, ['status' => 'active']);
    }
    
    // 取消訂閱
    public function cancelSubscription($id) {
        return $this->update($id, ['status' => 'cancelled']);
    }
    
    // 獲取熱門分類
    public function getPopularCategories($limit = 10) {
        $sql = "SELECT category, COUNT(*) as count, SUM(price) as total_cost
                FROM {$this->table} 
                WHERE deleted_at IS NULL 
                GROUP BY category 
                ORDER BY count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取最昂貴的訂閱
    public function getMostExpensiveSubscriptions($limit = 10) {
        return $this->all([], 'price DESC', $limit);
    }
}
?>