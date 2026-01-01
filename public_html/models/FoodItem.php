<?php
require_once 'BaseModel.php';

class FoodItem extends BaseModel {
    protected $table = 'food_items';
    
    protected $fillable = [
        'name', 'brand', 'category', 'quantity', 'unit', 'price', 'currency',
        'purchase_date', 'expiry_date', 'location', 'barcode', 'image_path',
        'notes', 'nutritional_info', 'tags', 'status', 'reminder_days'
    ];
    
    protected $hidden = [];
    
    // 獲取所有食品，按到期日期排序
    public function getAllFoods($orderBy = 'expiry_date ASC', $limit = null) {
        return $this->all([], $orderBy, $limit);
    }
    
    // 根據分類獲取食品
    public function getByCategory($category, $limit = null) {
        return $this->all(['category' => $category], 'expiry_date ASC', $limit);
    }
    
    // 根據狀態獲取食品
    public function getByStatus($status, $limit = null) {
        return $this->all(['status' => $status], 'expiry_date ASC', $limit);
    }
    
    // 搜尋食品
    public function searchFoods($query, $limit = null) {
        return $this->search($query, ['name', 'brand', 'location', 'notes'], [], 'expiry_date ASC', $limit);
    }
    
    // 獲取即將到期的食品
    public function getExpiringFoods($days = 7) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND expiry_date >= CURDATE()
                AND deleted_at IS NULL 
                ORDER BY expiry_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取已過期的食品
    public function getExpiredFoods() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE expiry_date < CURDATE()
                AND deleted_at IS NULL 
                ORDER BY expiry_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 獲取統計信息
    public function getStatistics() {
        $total = $this->count();
        
        // 3天內到期
        $expiring3Days = count($this->getExpiringFoods(3));
        
        // 7天內到期
        $expiring7Days = count($this->getExpiringFoods(7));
        
        // 30天內到期
        $expiring30Days = count($this->getExpiringFoods(30));
        
        // 已過期
        $expired = count($this->getExpiredFoods());
        
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
        
        // 總價值
        $sql = "SELECT SUM(price * quantity) as total_value FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalValue = $stmt->fetchColumn();
        
        return [
            'total' => $total,
            'expiring_3_days' => $expiring3Days,
            'expiring_7_days' => $expiring7Days,
            'expiring_30_days' => $expiring30Days,
            'expired' => $expired,
            'total_value' => $totalValue ?: 0,
            'category_stats' => $categoryStats,
            'status_stats' => $statusStats
        ];
    }
    
    // 創建食品記錄
    public function createFood($data) {
        // 處理營養信息
        if (isset($data['nutritional_info']) && is_array($data['nutritional_info'])) {
            $data['nutritional_info'] = json_encode($data['nutritional_info']);
        }
        
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        // 自動設置狀態
        if (!isset($data['status'])) {
            $data['status'] = $this->calculateStatus($data['expiry_date']);
        }
        
        return $this->create($data);
    }
    
    // 更新食品記錄
    public function updateFood($id, $data) {
        // 處理營養信息
        if (isset($data['nutritional_info']) && is_array($data['nutritional_info'])) {
            $data['nutritional_info'] = json_encode($data['nutritional_info']);
        }
        
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        // 自動更新狀態
        if (isset($data['expiry_date'])) {
            $data['status'] = $this->calculateStatus($data['expiry_date']);
        }
        
        return $this->update($id, $data);
    }
    
    // 計算食品狀態
    private function calculateStatus($expiryDate) {
        $today = new DateTime();
        $expiry = new DateTime($expiryDate);
        $diff = $today->diff($expiry);
        
        if ($expiry < $today) {
            return 'expired';
        } elseif ($diff->days <= 3) {
            return 'warning';
        } else {
            return 'fresh';
        }
    }
    
    // 更新所有食品狀態
    public function updateAllStatuses() {
        $sql = "UPDATE {$this->table} SET 
                status = CASE 
                    WHEN expiry_date < CURDATE() THEN 'expired'
                    WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 'warning'
                    ELSE 'fresh'
                END,
                updated_at = NOW()
                WHERE deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
    
    // 獲取需要提醒的食品
    public function getFoodsNeedingReminder() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL reminder_days DAY)
                AND expiry_date >= CURDATE()
                AND deleted_at IS NULL 
                ORDER BY expiry_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 獲取熱門分類
    public function getPopularCategories($limit = 10) {
        $sql = "SELECT category, COUNT(*) as count 
                FROM {$this->table} 
                WHERE deleted_at IS NULL 
                GROUP BY category 
                ORDER BY count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取熱門品牌
    public function getPopularBrands($limit = 10) {
        $sql = "SELECT brand, COUNT(*) as count 
                FROM {$this->table} 
                WHERE brand IS NOT NULL 
                AND deleted_at IS NULL 
                GROUP BY brand 
                ORDER BY count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
}
?>