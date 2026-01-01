<?php
require_once 'BaseModel.php';

class Gallery extends BaseModel {
    protected $table = 'gallery';
    
    protected $fillable = [
        'filename', 'original_name', 'title', 'description', 'file_path',
        'file_size', 'file_type', 'mime_type', 'width', 'height',
        'tags', 'category', 'is_ai_generated', 'metadata'
    ];
    
    protected $hidden = [];
    
    // 獲取所有圖片，按創建時間倒序
    public function getAllImages($limit = null) {
        return $this->all([], 'created_at DESC', $limit);
    }
    
    // 根據分類獲取圖片
    public function getByCategory($category, $limit = null) {
        return $this->all(['category' => $category], 'created_at DESC', $limit);
    }
    
    // 搜尋圖片
    public function searchImages($query, $limit = null) {
        return $this->search($query, ['title', 'original_name', 'filename', 'description'], [], 'created_at DESC', $limit);
    }
    
    // 獲取AI生成的圖片
    public function getAIGeneratedImages($limit = null) {
        return $this->all(['is_ai_generated' => 1], 'created_at DESC', $limit);
    }
    
    // 獲取統計信息
    public function getStatistics() {
        $total = $this->count();
        
        // 按文件類型統計
        $sql = "SELECT file_type, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY file_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $typeStats = $stmt->fetchAll();
        
        // 計算總大小
        $sql = "SELECT SUM(file_size) as total_size FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalSize = $stmt->fetchColumn();
        
        // AI生成圖片數量
        $aiCount = $this->count(['is_ai_generated' => 1]);
        
        return [
            'total' => $total,
            'total_size' => $totalSize ?: 0,
            'total_size_formatted' => $this->formatFileSize($totalSize ?: 0),
            'ai_generated' => $aiCount,
            'type_stats' => $typeStats
        ];
    }
    
    // 格式化文件大小
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    // 創建圖片記錄
    public function createImage($data) {
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        // 處理元數據
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }
        
        return $this->create($data);
    }
    
    // 更新圖片記錄
    public function updateImage($id, $data) {
        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        // 處理元數據
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }
        
        return $this->update($id, $data);
    }
    
    // 獲取最近上傳的圖片
    public function getRecentImages($days = 7, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                AND deleted_at IS NULL 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days, $limit]);
        
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
}
?>