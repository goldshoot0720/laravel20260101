<?php
require_once 'BaseModel.php';

class Video extends BaseModel {
    protected $table = 'videos';
    
    protected $fillable = [
        'filename', 'original_name', 'title', 'description', 'file_path',
        'thumbnail_path', 'file_size', 'file_type', 'mime_type', 'duration',
        'width', 'height', 'bitrate', 'fps', 'tags', 'category', 'metadata'
    ];
    
    protected $hidden = [];
    
    // 獲取所有影片，按創建時間倒序
    public function getAllVideos($limit = null) {
        return $this->all([], 'created_at DESC', $limit);
    }
    
    // 根據分類獲取影片
    public function getByCategory($category, $limit = null) {
        return $this->all(['category' => $category], 'created_at DESC', $limit);
    }
    
    // 搜尋影片
    public function searchVideos($query, $limit = null) {
        return $this->search($query, ['title', 'original_name', 'filename', 'description'], [], 'created_at DESC', $limit);
    }
    
    // 獲取統計信息
    public function getStatistics() {
        $total = $this->count();
        
        // 計算總大小
        $sql = "SELECT SUM(file_size) as total_size FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalSize = $stmt->fetchColumn();
        
        // 計算總時長
        $sql = "SELECT SUM(duration) as total_duration FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalDuration = $stmt->fetchColumn();
        
        // 按文件類型統計
        $sql = "SELECT file_type, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY file_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $typeStats = $stmt->fetchAll();
        
        return [
            'total' => $total,
            'total_size' => $totalSize ?: 0,
            'total_size_formatted' => $this->formatFileSize($totalSize ?: 0),
            'total_duration' => $totalDuration ?: 0,
            'total_duration_formatted' => $this->formatDuration($totalDuration ?: 0),
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
    
    // 格式化時長
    private function formatDuration($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }
    
    // 創建影片記錄
    public function createVideo($data) {
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
    
    // 更新影片記錄
    public function updateVideo($id, $data) {
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
    
    // 增加觀看次數
    public function incrementViewCount($id) {
        $sql = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // 獲取最近上傳的影片
    public function getRecentVideos($days = 7, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                AND deleted_at IS NULL 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days, $limit]);
        
        return $stmt->fetchAll();
    }
    
    // 獲取熱門影片
    public function getPopularVideos($limit = 10) {
        return $this->all([], 'view_count DESC', $limit);
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