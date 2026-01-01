<?php
// 資料庫配置文件

// 檢測環境（本地或遠端）
function isLocalEnvironment() {
    $localIndicators = [
        isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost',
        isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === '127.0.0.1',
        isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0,
        isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost',
        !isset($_SERVER['HTTP_HOST']) // CLI 環境
    ];
    
    return in_array(true, $localIndicators);
}

// 資料庫配置
$dbConfig = [
    'local' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'feng_laravel',
        'charset' => 'utf8mb4',
        'port' => 3306
    ],
    'production' => [
        'host' => 'localhost',
        'username' => 'feng_laravel',
        'password' => 'ym0Tagood129',
        'database' => 'feng_laravel',
        'charset' => 'utf8mb4',
        'port' => 3306
    ]
];

// 選擇配置
$environment = isLocalEnvironment() ? 'local' : 'production';
$config = $dbConfig[$environment];

// 定義常數
define('DB_HOST', $config['host']);
define('DB_USERNAME', $config['username']);
define('DB_PASSWORD', $config['password']);
define('DB_DATABASE', $config['database']);
define('DB_CHARSET', $config['charset']);
define('DB_PORT', $config['port']);
define('DB_ENVIRONMENT', $environment);

// 資料庫連接類
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
            
            // 設置時區
            $this->connection->exec("SET time_zone = '+08:00'");
            
        } catch (PDOException $e) {
            error_log("資料庫連接失敗: " . $e->getMessage());
            throw new Exception("資料庫連接失敗，請檢查配置");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // 防止複製
    private function __clone() {}
    
    // 防止反序列化
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// 全域資料庫連接函數
function getDB() {
    return Database::getInstance()->getConnection();
}

// 測試資料庫連接
function testDatabaseConnection() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT 1 as test");
        $result = $stmt->fetch();
        return $result['test'] === 1;
    } catch (Exception $e) {
        error_log("資料庫測試失敗: " . $e->getMessage());
        return false;
    }
}
?>