<?php
// PHP 內建服務器路由器

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$decodedPath = urldecode($path);

// 如果請求的是實際存在的文件，直接返回
if ($decodedPath !== '/' && file_exists(__DIR__ . $decodedPath) && is_file(__DIR__ . $decodedPath)) {
    return false; // 讓內建服務器處理
}

// 否則路由到 index.php
require_once __DIR__ . '/index.php';
?>
