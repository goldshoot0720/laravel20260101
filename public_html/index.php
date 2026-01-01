<?php
session_start();

// 基本配置
define('BASE_PATH', __DIR__);
define('SYSTEM_NAME', '鋒兄AI資訊系統');
define('VERSION', '1.0.0');

// 載入資料庫配置
require_once 'config/database.php';

// 測試資料庫連接
try {
    if (!testDatabaseConnection()) {
        error_log("資料庫連接測試失敗");
    }
} catch (Exception $e) {
    error_log("資料庫連接錯誤: " . $e->getMessage());
}

// 路由處理
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// 如果是空路徑，顯示主頁
if (empty($path)) {
    include 'views/dashboard.php';
    exit;
}

// 路由映射
$routes = [
    'dashboard' => 'views/dashboard.php',
    'food' => 'views/food.php',
    'subscription' => 'views/subscription.php',
    'api' => 'api/handler.php'
];

// 處理路由
if (isset($routes[$path])) {
    include $routes[$path];
} else {
    http_response_code(404);
    include 'views/404.php';
}
?>