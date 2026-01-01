<?php
require_once 'BaseModel.php';

class Food extends BaseModel {
    protected $table = 'food';
    
    protected $fillable = [
        'name', 'todate', 'amount', 'photo', 'price', 'shop', 'photohash'
    ];
    
    protected $hidden = [];
    protected $timestamps = false; // 原表沒有時間戳
    
    // 獲取所有食品，按到期日期排序
    public function getAllFoods($orderBy = 'todate ASC', $limit = null) {
        return $this->all([], $orderBy, $limit);
    }
    
    // 搜尋食品
    public function searchFoods($query, $limit = null) {
        return $this->search($query, ['name', 'shop'], [], 'todate ASC', $limit);
    }
    
    // 獲取即將到期的食品
    public function getExpiringFoods($days = 7) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE todate <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND todate >= CURDATE()
                ORDER BY todate ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取已過期的食品
    public function getExpiredFoods() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE todate < CURDATE()
                ORDER BY todate DESC";
        
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
        
        // 總價值
        $sql = "SELECT SUM(price * amount) as total_value FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalValue = $stmt->fetchColumn();
        
        return [
            'total' => $total,
            'expiring_3_days' => $expiring3Days,
            'expiring_7_days' => $expiring7Days,
            'expiring_30_days' => $expiring30Days,
            'expired' => $expired,
            'total_value' => $totalValue ?: 0
        ];
    }
    
    // 創建食品記錄
    public function createFood($data) {
        // 映射字段名
        $mappedData = [];
        if (isset($data['name'])) $mappedData['name'] = $data['name'];
        if (isset($data['expiry_date'])) $mappedData['todate'] = $data['expiry_date'];
        if (isset($data['quantity'])) $mappedData['amount'] = $data['quantity'];
        if (isset($data['image_path'])) $mappedData['photo'] = $data['image_path'];
        if (isset($data['price'])) $mappedData['price'] = $data['price'];
        if (isset($data['location'])) $mappedData['shop'] = $data['location'];
        
        return $this->create($mappedData);
    }
    
    // 更新食品記錄
    public function updateFood($id, $data) {
        // 映射字段名
        $mappedData = [];
        if (isset($data['name'])) $mappedData['name'] = $data['name'];
        if (isset($data['expiry_date'])) $mappedData['todate'] = $data['expiry_date'];
        if (isset($data['quantity'])) $mappedData['amount'] = $data['quantity'];
        if (isset($data['image_path'])) $mappedData['photo'] = $data['image_path'];
        if (isset($data['price'])) $mappedData['price'] = $data['price'];
        if (isset($data['location'])) $mappedData['shop'] = $data['location'];
        
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
    
    // 檢查表是否有軟刪除支援
    protected function hasSoftDeletes() {
        return false; // 原表沒有 deleted_at 欄位
    }
}
?>