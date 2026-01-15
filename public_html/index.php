<?php
session_start();

// 基本配置
define('BASE_PATH', __DIR__);
define('SYSTEM_NAME', '鋒兄塗哥公關資訊');
define('VERSION', '1.0.0');

// 路由處理
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// 載入資料庫配置
require_once __DIR__ . '/config/database.php';

// 如果是空路徑，顯示主頁
if (empty($path)) {
    include 'views/gallery.php';
    exit;
}

// 路由映射
$routes = [
    'dashboard' => 'views/dashboard.php',
    'gallery' => 'views/gallery.php',
    'videos' => 'views/videos.php',
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
