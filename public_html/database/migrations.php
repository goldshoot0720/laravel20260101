<?php
// 資料庫遷移文件

require_once __DIR__ . '/../config/database.php';

class DatabaseMigration {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // 執行所有遷移
    public function runMigrations() {
        echo "開始執行資料庫遷移...\n";
        echo "環境: " . DB_ENVIRONMENT . "\n";
        echo "資料庫: " . DB_DATABASE . "\n\n";
        
        try {
            $this->createMigrationsTable();
            $this->createFoodTable();
            $this->createSubscriptionTable();
            
            echo "✅ 所有資料庫遷移完成！\n";
            
        } catch (Exception $e) {
            echo "❌ 遷移失敗: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    // 創建遷移記錄表
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 migrations 表\n";
    }
    
    // 創建食品管理表
    private function createFoodTable() {
        $sql = "CREATE TABLE IF NOT EXISTS food (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name TEXT NOT NULL,
            todate DATE NOT NULL,
            amount INT NOT NULL DEFAULT 1,
            price INT DEFAULT 0,
            shop TEXT,
            photohash TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 food 表\n";
    }
    
    // 創建訂閱管理表
    private function createSubscriptionTable() {
        $sql = "CREATE TABLE IF NOT EXISTS subscription (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name TEXT NOT NULL,
            website_url TEXT,
            category TEXT,
            price INT NOT NULL DEFAULT 0,
            billing_cycle TEXT,
            start_date DATE NOT NULL,
            next_payment_date DATE NOT NULL,
            status TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 subscription 表\n";
    }
    
    // 插入初始數據
    public function seedData() {
        echo "\n開始插入初始數據...\n";
        
        try {
            $this->seedFoodData();
            $this->seedSubscriptionData();
            
            echo "✅ 初始數據插入完成！\n";
            
        } catch (Exception $e) {
            echo "❌ 數據插入失敗: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    // 插入食品管理示例數據
    private function seedFoodData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  food 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleFoods = [
            [
                'name' => '【張君雅】五香海苔休閒丸子',
                'todate' => date('Y-m-d', strtotime('+15 days')),
                'amount' => 3,
                'price' => 150,
                'shop' => '廚房櫃子'
            ],
            [
                'name' => '【張君雅】日式串燒休閒丸子',
                'todate' => date('Y-m-d', strtotime('+16 days')),
                'amount' => 6,
                'price' => 300,
                'shop' => '廚房櫃子'
            ],
            [
                'name' => '有機蘋果',
                'todate' => date('Y-m-d', strtotime('+7 days')),
                'amount' => 5,
                'price' => 150,
                'shop' => '冰箱'
            ]
        ];
        
        $sql = "INSERT INTO food (name, todate, amount, price, shop) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleFoods as $food) {
            $stmt->execute([
                $food['name'],
                $food['todate'],
                $food['amount'],
                $food['price'],
                $food['shop']
            ]);
        }
        
        echo "✅ 插入 food 示例數據\n";
    }
    
    // 插入訂閱管理示例數據
    private function seedSubscriptionData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM subscription");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  subscription 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleSubscriptions = [
            [
                'name' => '天虎/黃信訊/心臟內科',
                'website_url' => 'https://www.tcmg.com.tw/index.php/main/schedule_time?id=18',
                'category' => 'medical',
                'price' => 530,
                'billing_cycle' => 'monthly',
                'start_date' => date('Y-m-d', strtotime('-30 days')),
                'next_payment_date' => date('Y-m-d', strtotime('+1 day')),
                'status' => 'active'
            ],
            [
                'name' => 'kiro pro',
                'website_url' => 'https://app.kiro.dev/account/',
                'category' => 'software',
                'price' => 640,
                'billing_cycle' => 'monthly',
                'start_date' => date('Y-m-d', strtotime('-20 days')),
                'next_payment_date' => date('Y-m-d', strtotime('+10 days')),
                'status' => 'active'
            ]
        ];
        
        $sql = "INSERT INTO subscription (name, website_url, category, price, billing_cycle, start_date, next_payment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleSubscriptions as $sub) {
            $stmt->execute([
                $sub['name'],
                $sub['website_url'],
                $sub['category'],
                $sub['price'],
                $sub['billing_cycle'],
                $sub['start_date'],
                $sub['next_payment_date'],
                $sub['status']
            ]);
        }
        
        echo "✅ 插入 subscription 示例數據\n";
    }
}

// 如果直接執行此文件，運行遷移
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $migration = new DatabaseMigration();
        $migration->runMigrations();
        $migration->seedData();
        
        echo "\n🎉 資料庫初始化完成！\n";
        echo "環境: " . DB_ENVIRONMENT . "\n";
        echo "資料庫: " . DB_DATABASE . "\n";
        
    } catch (Exception $e) {
        echo "\n💥 初始化失敗: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>