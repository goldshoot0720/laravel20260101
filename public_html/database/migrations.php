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
            $this->createFoodTable();
            $this->createSubscriptionTable();
            
            echo "✅ 所有資料庫遷移完成！\n";
            
        } catch (Exception $e) {
            echo "❌ 遷移失敗: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    // 創建食品管理表
    private function createFoodTable() {
        $sql = "CREATE TABLE IF NOT EXISTS food (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name TEXT,
            todate DATE,
            amount INT,
            photo TEXT,
            price INT,
            shop TEXT,
            photohash TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
        $this->db->exec($sql);
        echo "✅ 創建 food 表\n";
    }
    
    // 創建訂閱管理表
    private function createSubscriptionTable() {
        $sql = "CREATE TABLE IF NOT EXISTS subscription (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name TEXT,
            nextdate DATE,
            price INT,
            site TEXT,
            note TEXT,
            account TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
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
                'todate' => '2026-01-06',
                'amount' => 3,
                'photo' => 'https://img.pchome.com.tw/cs/items/DBACC4A90089CJA/000001_1689668194.jpg',
                'price' => null,
                'shop' => null,
                'photohash' => null
            ],
            [
                'name' => '【張君雅】日式串燒休閒丸子',
                'todate' => '2026-01-07',
                'amount' => 6,
                'photo' => 'https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg',
                'price' => 0,
                'shop' => '',
                'photohash' => ''
            ]
        ];
        
        $sql = "INSERT INTO food (name, todate, amount, photo, price, shop, photohash) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleFoods as $food) {
            $stmt->execute([
                $food['name'],
                $food['todate'],
                $food['amount'],
                $food['photo'],
                $food['price'],
                $food['shop'],
                $food['photohash']
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
                'name' => 'kiro pro',
                'nextdate' => '2026-01-01',
                'price' => 640,
                'site' => 'https://app.kiro.dev/account/usage',
                'note' => null,
                'account' => null
            ],
            [
                'name' => '自然輸入法/ 已經取消訂閱。',
                'nextdate' => '2026-01-03',
                'price' => 129,
                'site' => 'https://service.iqt.ai/AccountInfo',
                'note' => '',
                'account' => ''
            ]
        ];
        
        $sql = "INSERT INTO subscription (name, nextdate, price, site, note, account) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($sampleSubscriptions as $sub) {
            $stmt->execute([
                $sub['name'],
                $sub['nextdate'],
                $sub['price'],
                $sub['site'],
                $sub['note'],
                $sub['account']
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