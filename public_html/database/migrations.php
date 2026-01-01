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
            $this->createSubscriptionsTable();
            
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
        $sql = "CREATE TABLE IF NOT EXISTS food_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            brand VARCHAR(255) DEFAULT NULL,
            category VARCHAR(100) DEFAULT 'general',
            quantity INT NOT NULL DEFAULT 1,
            unit VARCHAR(50) DEFAULT '個',
            price DECIMAL(10,2) DEFAULT 0.00,
            currency VARCHAR(10) DEFAULT 'TWD',
            purchase_date DATE DEFAULT NULL,
            expiry_date DATE NOT NULL,
            location VARCHAR(255) DEFAULT NULL,
            barcode VARCHAR(100) DEFAULT NULL,
            image_path VARCHAR(500) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            nutritional_info JSON DEFAULT NULL,
            tags JSON DEFAULT NULL,
            status ENUM('fresh', 'warning', 'expired', 'consumed') DEFAULT 'fresh',
            reminder_days INT DEFAULT 7 COMMENT '提前提醒天數',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_expiry_date (expiry_date),
            INDEX idx_category (category),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 food_items 表\n";
    }
    
    // 創建訂閱管理表
    private function createSubscriptionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            website_url VARCHAR(500) DEFAULT NULL,
            category VARCHAR(100) DEFAULT 'general',
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            currency VARCHAR(10) DEFAULT 'TWD',
            billing_cycle ENUM('monthly', 'quarterly', 'yearly', 'one-time') DEFAULT 'monthly',
            start_date DATE NOT NULL,
            next_payment_date DATE NOT NULL,
            last_payment_date DATE DEFAULT NULL,
            auto_renewal BOOLEAN DEFAULT TRUE,
            payment_method VARCHAR(100) DEFAULT NULL,
            account_email VARCHAR(255) DEFAULT NULL,
            account_info JSON DEFAULT NULL,
            reminder_days INT DEFAULT 7 COMMENT '提前提醒天數',
            status ENUM('active', 'paused', 'cancelled', 'expired') DEFAULT 'active',
            notes TEXT DEFAULT NULL,
            tags JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_next_payment_date (next_payment_date),
            INDEX idx_category (category),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 subscriptions 表\n";
    }
    
    // 插入初始數據
    public function seedData() {
        echo "\n開始插入初始數據...\n";
        
        try {
            $this->seedFoodData();
            $this->seedSubscriptionsData();
            
            echo "✅ 初始數據插入完成！\n";
            
        } catch (Exception $e) {
            echo "❌ 數據插入失敗: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    // 插入食品管理示例數據
    private function seedFoodData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_items WHERE deleted_at IS NULL");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  food_items 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleFoods = [
            [
                'name' => '【張君雅】五香海苔休閒丸子',
                'brand' => '張君雅',
                'category' => 'snacks',
                'quantity' => 3,
                'unit' => '包',
                'expiry_date' => date('Y-m-d', strtotime('+15 days')),
                'location' => '廚房櫃子',
                'status' => 'fresh'
            ],
            [
                'name' => '【張君雅】日式串燒休閒丸子',
                'brand' => '張君雅',
                'category' => 'snacks',
                'quantity' => 6,
                'unit' => '包',
                'expiry_date' => date('Y-m-d', strtotime('+16 days')),
                'location' => '廚房櫃子',
                'status' => 'fresh'
            ],
            [
                'name' => '有機蘋果',
                'category' => 'fruits',
                'quantity' => 5,
                'unit' => '顆',
                'price' => 150.00,
                'expiry_date' => date('Y-m-d', strtotime('+7 days')),
                'location' => '冰箱',
                'status' => 'warning'
            ]
        ];
        
        $sql = "INSERT INTO food_items (name, brand, category, quantity, unit, price, expiry_date, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleFoods as $food) {
            $stmt->execute([
                $food['name'],
                $food['brand'] ?? null,
                $food['category'],
                $food['quantity'],
                $food['unit'],
                $food['price'] ?? 0,
                $food['expiry_date'],
                $food['location'],
                $food['status']
            ]);
        }
        
        echo "✅ 插入 food_items 示例數據\n";
    }
    
    // 插入訂閱管理示例數據
    private function seedSubscriptionsData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM subscriptions WHERE deleted_at IS NULL");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  subscriptions 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleSubscriptions = [
            [
                'name' => '天虎/黃信訊/心臟內科',
                'website_url' => 'https://www.tcmg.com.tw/index.php/main/schedule_time?id=18',
                'category' => 'medical',
                'price' => 530.00,
                'billing_cycle' => 'monthly',
                'start_date' => date('Y-m-d', strtotime('-30 days')),
                'next_payment_date' => date('Y-m-d', strtotime('+1 day')),
                'status' => 'active'
            ],
            [
                'name' => 'kiro pro',
                'website_url' => 'https://app.kiro.dev/account/',
                'category' => 'software',
                'price' => 640.00,
                'billing_cycle' => 'monthly',
                'start_date' => date('Y-m-d', strtotime('-20 days')),
                'next_payment_date' => date('Y-m-d', strtotime('+10 days')),
                'status' => 'active'
            ]
        ];
        
        $sql = "INSERT INTO subscriptions (name, website_url, category, price, billing_cycle, start_date, next_payment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
        
        echo "✅ 插入 subscriptions 示例數據\n";
    }
    
    // 插入系統設置
    private function seedSystemSettings() {
        $settings = [
            ['system_name', '鋒兄AI資訊系統', 'string', '系統名稱', true],
            ['system_version', '1.0.0', 'string', '系統版本', true],
            ['max_upload_size', '10485760', 'number', '最大上傳文件大小(bytes)', false],
            ['allowed_image_types', '["jpg","jpeg","png","gif","webp"]', 'json', '允許的圖片格式', false],
            ['allowed_video_types', '["mp4","avi","mov","wmv","flv"]', 'json', '允許的影片格式', false],
            ['default_reminder_days', '7', 'number', '默認提醒天數', false],
            ['enable_notifications', 'true', 'boolean', '啟用通知功能', false]
        ];
        
        $sql = "INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }
        
        echo "✅ 插入系統設置\n";
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