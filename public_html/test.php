<?php
// 測試腳本
require_once 'config/database.php';
require_once 'models/Food.php';
require_once 'models/Subscription.php';

echo "<h1>🔥 鋒兄AI資訊系統 - 本地測試</h1>";
echo "<style>body{font-family:'Microsoft JhengHei';background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:20px;}</style>";

try {
    // 測試資料庫連接
    echo "<h2>📊 資料庫連接測試</h2>";
    $connected = testDatabaseConnection();
    echo $connected ? "✅ 資料庫連接成功<br>" : "❌ 資料庫連接失敗<br>";
    echo "環境: " . DB_ENVIRONMENT . "<br>";
    echo "資料庫: " . DB_DATABASE . "<br><br>";
    
    echo "<h2>🍎 食品管理測試</h2>";
    $food = new Food();
    $stats = $food->getStatistics();
    echo "總食品數: " . $stats['total'] . "<br>";
    echo "3天內到期: " . $stats['expiring_3_days'] . "<br>";
    echo "7天內到期: " . $stats['expiring_7_days'] . "<br><br>";
    
    echo "<h2>📋 訂閱管理測試</h2>";
    $subscription = new Subscription();
    $stats = $subscription->getStatistics();
    echo "總訂閱數: " . $stats['total'] . "<br>";
    echo "活躍訂閱: " . $stats['active'] . "<br>";
    echo "月度費用: NT$ " . number_format($stats['monthly_cost'], 2) . "<br><br>";
    
    echo "<h2>🎉 測試完成</h2>";
    echo "<p>所有功能正常運行！</p>";
    echo "<p><a href='/' style='color:#fbbf24;'>前往系統首頁</a></p>";
    echo "<p><a href='/install.php' style='color:#fbbf24;'>查看安裝頁面</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ 測試失敗</h2>";
    echo "<p>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>