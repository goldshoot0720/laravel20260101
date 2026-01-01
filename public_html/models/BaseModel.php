<?php
// 基礎模型類

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // 查找所有記錄
    public function all($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    $placeholders = str_repeat('?,', count($value) - 1) . '?';
                    $whereClause[] = "{$key} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereClause[] = "{$key} = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // 軟刪除過濾
        if ($this->hasSoftDeletes()) {
            $sql .= empty($conditions) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // 根據ID查找記錄
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        if ($this->hasSoftDeletes()) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
    
    // 根據條件查找第一條記錄
    public function findWhere($conditions) {
        $results = $this->all($conditions, null, 1);
        return !empty($results) ? $results[0] : null;
    }
    
    // 創建新記錄
    public function create($data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = array_keys($data);
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->find($this->db->lastInsertId());
    }
    
    // 更新記錄
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(',', $setClause) . " WHERE {$this->primaryKey} = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $this->find($id);
    }
    
    // 刪除記錄
    public function delete($id) {
        if ($this->hasSoftDeletes()) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        }
    }
    
    // 永久刪除記錄
    public function forceDelete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // 恢復軟刪除的記錄
    public function restore($id) {
        if ($this->hasSoftDeletes()) {
            return $this->updateWithoutFilter($id, ['deleted_at' => null]);
        }
        return false;
    }
    
    // 計數
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "{$key} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($this->hasSoftDeletes()) {
            $sql .= empty($conditions) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    // 分頁查詢
    public function paginate($page = 1, $perPage = 15, $conditions = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "{$key} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($this->hasSoftDeletes()) {
            $sql .= empty($conditions) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $data = $stmt->fetchAll();
        $total = $this->count($conditions);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    // 搜尋
    public function search($query, $columns = [], $conditions = [], $orderBy = null, $limit = null) {
        if (empty($columns)) {
            return [];
        }
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereClause = [];
        
        // 搜尋條件
        $searchClause = [];
        foreach ($columns as $column) {
            $searchClause[] = "{$column} LIKE ?";
            $params[] = "%{$query}%";
        }
        $whereClause[] = "(" . implode(' OR ', $searchClause) . ")";
        
        // 額外條件
        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = ?";
            $params[] = $value;
        }
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // 軟刪除過濾
        if ($this->hasSoftDeletes()) {
            $sql .= empty($whereClause) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // 過濾可填充字段
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    // 隱藏字段
    protected function hideFields($data) {
        if (empty($this->hidden)) {
            return $data;
        }
        
        if (is_array($data) && isset($data[0])) {
            // 多條記錄
            return array_map(function($item) {
                return array_diff_key($item, array_flip($this->hidden));
            }, $data);
        } else {
            // 單條記錄
            return array_diff_key($data, array_flip($this->hidden));
        }
    }
    
    // 檢查是否支持軟刪除
    protected function hasSoftDeletes() {
        return in_array('deleted_at', $this->getTableColumns());
    }
    
    // 獲取表格欄位
    protected function getTableColumns() {
        static $columns = [];
        
        if (!isset($columns[$this->table])) {
            $stmt = $this->db->prepare("DESCRIBE {$this->table}");
            $stmt->execute();
            $columns[$this->table] = array_column($stmt->fetchAll(), 'Field');
        }
        
        return $columns[$this->table];
    }
    
    // 不過濾字段的更新方法
    private function updateWithoutFilter($id, $data) {
        if ($this->timestamps && !isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(',', $setClause) . " WHERE {$this->primaryKey} = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $this->find($id);
    }
}
?>