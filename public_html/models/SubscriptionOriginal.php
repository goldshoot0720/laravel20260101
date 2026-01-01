<?php
require_once 'BaseModel.php';

class SubscriptionOriginal extends BaseModel {
    protected $table = 'subscription';
    
    protected $fillable = [
        'name', 'site', 'price', 'nextdate', 'note', 'account'
    ];
    
    protected $hidden = [];
    protected $timestamps = false; // 原表沒有時間戳
    
    // 獲取所有訂閱，按到期日期排序
    public function getAllSubscriptions($orderBy = 'nextdate ASC', $limit = null) {
        return $this->all([], $orderBy, $limit);
    }
    
    // 搜尋訂閱
    public function searchSubscriptions($query, $limit = null) {
        return $this->search($query, ['name', 'site', 'note'], [], 'nextdate ASC', $limit);
    }
    
    // 獲取即將到期的訂閱
    public function getExpiringSubscriptions($days = 7) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nextdate <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND nextdate >= CURDATE()
                ORDER BY nextdate ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取已過期的訂閱
    public function getExpiredSubscriptions() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nextdate < CURDATE()
                ORDER BY nextdate DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 獲取活躍訂閱（未過期）
    public function getActiveSubscriptions($limit = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nextdate >= CURDATE()
                ORDER BY nextdate ASC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 獲取統計信息
    public function getStatistics() {
        $total = $this->count();
        $active = count($this->getActiveSubscriptions());
        
        // 3天內到期
        $expiring3Days = count($this->getExpiringSubscriptions(3));
        
        // 7天內到期
        $expiring7Days = count($this->getExpiringSubscriptions(7));
        
        // 30天內到期
        $expiring30Days = count($this->getExpiringSubscriptions(30));
        
        // 已過期
        $expired = count($this->getExpiredSubscriptions());
        
        // 月度總支出（假設都是月付）
        $sql = "SELECT SUM(price) as monthly_cost FROM {$this->table} WHERE nextdate >= CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $monthlyCost = $stmt->fetchColumn();
        
        // 年度總支出
        $yearlyCost = $monthlyCost * 12;
        
        return [
            'total' => $total,
            'active' => $active,
            'expiring_3_days' => $expiring3Days,
            'expiring_7_days' => $expiring7Days,
            'expiring_30_days' => $expiring30Days,
            'expired' => $expired,
            'monthly_cost' => $monthlyCost ?: 0,
            'yearly_cost' => $yearlyCost ?: 0
        ];
    }
    
    // 創建訂閱記錄
    public function createSubscription($data) {
        // 映射字段名
        $mappedData = [];
        if (isset($data['name'])) $mappedData['name'] = $data['name'];
        if (isset($data['website_url'])) $mappedData['site'] = $data['website_url'];
        if (isset($data['price'])) $mappedData['price'] = $data['price'];
        if (isset($data['next_payment_date'])) $mappedData['nextdate'] = $data['next_payment_date'];
        if (isset($data['notes'])) $mappedData['note'] = $data['notes'];
        if (isset($data['account_email'])) $mappedData['account'] = $data['account_email'];
        
        return $this->create($mappedData);
    }
    
    // 更新訂閱記錄
    public function updateSubscription($id, $data) {
        // 映射字段名
        $mappedData = [];
        if (isset($data['name'])) $mappedData['name'] = $data['name'];
        if (isset($data['website_url'])) $mappedData['site'] = $data['website_url'];
        if (isset($data['price'])) $mappedData['price'] = $data['price'];
        if (isset($data['next_payment_date'])) $mappedData['nextdate'] = $data['next_payment_date'];
        if (isset($data['notes'])) $mappedData['note'] = $data['notes'];
        if (isset($data['account_email'])) $mappedData['account'] = $data['account_email'];
        
        return $this->update($id, $mappedData);
    }
    
    // 計算剩餘天數
    public function getDaysLeft($expiryDate) {
        $today = new DateTime();
        $expiry = new DateTime($expiryDate);
        $diff = $today->diff($expiry);
        
        if ($expiry < $today) {
            return -$diff->days; // 負數表示已過期
        }
        
        return $diff->days;
    }
    
    // 獲取狀態
    public function getStatus($expiryDate) {
        $daysLeft = $this->getDaysLeft($expiryDate);
        
        if ($daysLeft < 0) {
            return 'expired';
        } elseif ($daysLeft <= 3) {
            return 'error';
        } elseif ($daysLeft <= 7) {
            return 'warning';
        } else {
            return 'success';
        }
    }
    
    // 格式化價格
    public function formatPrice($price) {
        return 'NT$ ' . number_format($price, 0);
    }
    
    // 更新付款日期
    public function updatePaymentDate($id, $paymentDate = null) {
        if (!$paymentDate) {
            $paymentDate = date('Y-m-d');
        }
        
        // 假設是月付，下次付款日期為一個月後
        $nextPaymentDate = date('Y-m-d', strtotime($paymentDate . ' +1 month'));
        
        return $this->update($id, [
            'nextdate' => $nextPaymentDate
        ]);
    }
    
    // 獲取需要提醒的訂閱
    public function getSubscriptionsNeedingReminder($days = 7) {
        return $this->getExpiringSubscriptions($days);
    }
    
    // 檢查表是否有軟刪除支援
    protected function hasSoftDeletes() {
        return false; // 原表沒有 deleted_at 欄位
    }
}
?>