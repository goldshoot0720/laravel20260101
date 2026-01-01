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
            $this->createGalleryTable();
            $this->createVideosTable();
            $this->createFoodTable();
            $this->createSubscriptionsTable();
            $this->createUsersTable();
            $this->createSystemSettingsTable();
            
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
    
    // 創建圖片庫表
    private function createGalleryTable() {
        $sql = "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            title VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL DEFAULT 0,
            file_type VARCHAR(50) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            width INT DEFAULT NULL,
            height INT DEFAULT NULL,
            tags JSON DEFAULT NULL,
            category VARCHAR(100) DEFAULT 'general',
            is_ai_generated BOOLEAN DEFAULT FALSE,
            metadata JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_category (category),
            INDEX idx_file_type (file_type),
            INDEX idx_created_at (created_at),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 gallery 表\n";
    }
    
    // 創建影片庫表
    private function createVideosTable() {
        $sql = "CREATE TABLE IF NOT EXISTS videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            file_path VARCHAR(500) NOT NULL,
            thumbnail_path VARCHAR(500) DEFAULT NULL,
            file_size INT NOT NULL DEFAULT 0,
            file_type VARCHAR(50) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            duration INT DEFAULT NULL COMMENT '影片長度(秒)',
            width INT DEFAULT NULL,
            height INT DEFAULT NULL,
            bitrate INT DEFAULT NULL,
            fps DECIMAL(5,2) DEFAULT NULL,
            tags JSON DEFAULT NULL,
            category VARCHAR(100) DEFAULT 'general',
            metadata JSON DEFAULT NULL,
            view_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_category (category),
            INDEX idx_file_type (file_type),
            INDEX idx_created_at (created_at),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 videos 表\n";
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
    
    // 創建用戶表
    private function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            display_name VARCHAR(255) DEFAULT NULL,
            avatar_path VARCHAR(500) DEFAULT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
            last_login_at TIMESTAMP NULL DEFAULT NULL,
            email_verified_at TIMESTAMP NULL DEFAULT NULL,
            preferences JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_username (username),
            INDEX idx_email (email),
            INDEX idx_role (role),
            INDEX idx_status (status),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 users 表\n";
    }
    
    // 創建系統設置表
    private function createSystemSettingsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS system_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) NOT NULL UNIQUE,
            setting_value TEXT DEFAULT NULL,
            setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
            description TEXT DEFAULT NULL,
            is_public BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_setting_key (setting_key),
            INDEX idx_is_public (is_public)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 system_settings 表\n";
    }
    
    // 插入初始數據
    public function seedData() {
        echo "\n開始插入初始數據...\n";
        
        try {
            $this->seedGalleryData();
            $this->seedVideosData();
            $this->seedFoodData();
            $this->seedSubscriptionsData();
            $this->seedSystemSettings();
            
            echo "✅ 初始數據插入完成！\n";
            
        } catch (Exception $e) {
            echo "❌ 數據插入失敗: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    // 插入圖片庫示例數據
    private function seedGalleryData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM gallery WHERE deleted_at IS NULL");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  gallery 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleImages = [
            [
                'filename' => '1761405813-example.jpg',
                'original_name' => 'AI生成圖片1.jpg',
                'title' => 'AI生成的美麗風景',
                'file_path' => '/uploads/gallery/1761405813-example.jpg',
                'file_size' => 887000,
                'file_type' => 'JPG',
                'mime_type' => 'image/jpeg',
                'width' => 1024,
                'height' => 768,
                'category' => 'ai_generated',
                'is_ai_generated' => true
            ],
            [
                'filename' => '1761405863-sample.jpg',
                'original_name' => 'AI生成圖片2.jpg',
                'title' => 'AI創作的人物肖像',
                'file_path' => '/uploads/gallery/1761405863-sample.jpg',
                'file_size' => 731000,
                'file_type' => 'JPG',
                'mime_type' => 'image/jpeg',
                'width' => 800,
                'height' => 600,
                'category' => 'ai_generated',
                'is_ai_generated' => true
            ]
        ];
        
        $sql = "INSERT INTO gallery (filename, original_name, title, file_path, file_size, file_type, mime_type, width, height, category, is_ai_generated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleImages as $image) {
            $stmt->execute([
                $image['filename'],
                $image['original_name'],
                $image['title'],
                $image['file_path'],
                $image['file_size'],
                $image['file_type'],
                $image['mime_type'],
                $image['width'],
                $image['height'],
                $image['category'],
                $image['is_ai_generated']
            ]);
        }
        
        echo "✅ 插入 gallery 示例數據\n";
    }
    
    // 插入影片庫示例數據
    private function seedVideosData() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM videos WHERE deleted_at IS NULL");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "⏭️  videos 表已有數據，跳過插入\n";
            return;
        }
        
        $sampleVideos = [
            [
                'filename' => 'feng_legend_life.mp4',
                'original_name' => '鋒兄的傳奇人生.mp4',
                'title' => '鋒兄的傳奇人生',
                'description' => '鋒兄人生歷程紀錄片',
                'file_path' => '/uploads/videos/feng_legend_life.mp4',
                'file_size' => 2010000,
                'file_type' => 'MP4',
                'mime_type' => 'video/mp4',
                'duration' => 45,
                'category' => 'documentary'
            ],
            [
                'filename' => 'feng_evolution_show.mp4',
                'original_name' => '鋒兄進化Show.mp4',
                'title' => '鋒兄進化Show 🔥',
                'description' => '鋒兄進化歷程山歷程',
                'file_path' => '/uploads/videos/feng_evolution_show.mp4',
                'file_size' => 4210000,
                'file_type' => 'MP4',
                'mime_type' => 'video/mp4',
                'duration' => 83,
                'category' => 'show'
            ]
        ];
        
        $sql = "INSERT INTO videos (filename, original_name, title, description, file_path, file_size, file_type, mime_type, duration, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleVideos as $video) {
            $stmt->execute([
                $video['filename'],
                $video['original_name'],
                $video['title'],
                $video['description'],
                $video['file_path'],
                $video['file_size'],
                $video['file_type'],
                $video['mime_type'],
                $video['duration'],
                $video['category']
            ]);
        }
        
        echo "✅ 插入 videos 示例數據\n";
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