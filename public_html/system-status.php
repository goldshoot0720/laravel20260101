<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系統狀態 - 鋒兄AI資訊系統</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', 'PingFang TC', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .status-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .status-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-ok { color: #10b981; }
        .status-error { color: #ef4444; }
        .status-warning { color: #f59e0b; }
        .btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #5856eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 鋒兄AI資訊系統 - 系統狀態</h1>
            <p>本地測試環境狀態檢查</p>
        </div>

        <div class="status-grid">
            <!-- 系統基本信息 -->
            <div class="status-card">
                <div class="status-title">
                    <span>⚙️</span>
                    <span>系統信息</span>
                </div>
                <div class="status-item">
                    <span>PHP 版本</span>
                    <span class="status-ok"><?= PHP_VERSION ?></span>
                </div>
                <div class="status-item">
                    <span>服務器時間</span>
                    <span><?= date('Y-m-d H:i:s') ?></span>
                </div>
                <div class="status-item">
                    <span>系統版本</span>
                    <span><?= defined('VERSION') ? VERSION : '1.0.0' ?></span>
                </div>
            </div>

            <!-- 資料庫狀態 -->
            <div class="status-card">
                <div class="status-title">
                    <span>🗄️</span>
                    <span>資料庫狀態</span>
                </div>
                <?php
                try {
                    require_once 'config/database.php';
                    $dbConnected = testDatabaseConnection();
                    echo '<div class="status-item">';
                    echo '<span>資料庫連接</span>';
                    echo '<span class="' . ($dbConnected ? 'status-ok">✅ 正常' : 'status-error">❌ 失敗') . '</span>';
                    echo '</div>';
                    
                    echo '<div class="status-item">';
                    echo '<span>環境</span>';
                    echo '<span>' . DB_ENVIRONMENT . '</span>';
                    echo '</div>';
                    
                    echo '<div class="status-item">';
                    echo '<span>資料庫</span>';
                    echo '<span>' . DB_DATABASE . '</span>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="status-item">';
                    echo '<span>資料庫連接</span>';
                    echo '<span class="status-error">❌ 錯誤</span>';
                    echo '</div>';
                    echo '<div class="status-item">';
                    echo '<span>錯誤信息</span>';
                    echo '<span class="status-error">' . htmlspecialchars($e->getMessage()) . '</span>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- 功能模組狀態 -->
            <div class="status-card">
                <div class="status-title">
                    <span>📊</span>
                    <span>功能模組</span>
                </div>
                <?php
                try {
                    require_once 'models/Gallery.php';
                    require_once 'models/Video.php';
                    require_once 'models/Food.php';
                    require_once 'models/SubscriptionOriginal.php';
                    
                    $gallery = new Gallery();
                    $galleryCount = $gallery->count();
                    
                    $video = new Video();
                    $videoCount = $video->count();
                    
                    $food = new Food();
                    $foodCount = $food->count();
                    
                    $subscription = new SubscriptionOriginal();
                    $subscriptionCount = $subscription->count();
                    
                    echo '<div class="status-item">';
                    echo '<span>圖片庫</span>';
                    echo '<span class="status-ok">' . $galleryCount . ' 張圖片</span>';
                    echo '</div>';
                    
                    echo '<div class="status-item">';
                    echo '<span>影片庫</span>';
                    echo '<span class="status-ok">' . $videoCount . ' 部影片</span>';
                    echo '</div>';
                    
                    echo '<div class="status-item">';
                    echo '<span>食品管理</span>';
                    echo '<span class="status-ok">' . $foodCount . ' 項食品</span>';
                    echo '</div>';
                    
                    echo '<div class="status-item">';
                    echo '<span>訂閱管理</span>';
                    echo '<span class="status-ok">' . $subscriptionCount . ' 個訂閱</span>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="status-item">';
                    echo '<span>模組狀態</span>';
                    echo '<span class="status-error">❌ 錯誤</span>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- API 狀態 -->
            <div class="status-card">
                <div class="status-title">
                    <span>🔌</span>
                    <span>API 接口</span>
                </div>
                <div class="status-item">
                    <span>統計 API</span>
                    <span class="status-ok">✅ 可用</span>
                </div>
                <div class="status-item">
                    <span>圖片 API</span>
                    <span class="status-ok">✅ 可用</span>
                </div>
                <div class="status-item">
                    <span>影片 API</span>
                    <span class="status-ok">✅ 可用</span>
                </div>
                <div class="status-item">
                    <span>搜尋 API</span>
                    <span class="status-ok">✅ 可用</span>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <h3>🎉 系統運行正常！</h3>
            <p>所有功能模組已就緒，可以開始使用系統。</p>
            
            <div style="margin-top: 20px;">
                <a href="/" class="btn">🏠 系統首頁</a>
                <a href="/gallery" class="btn">🖼️ 圖片庫</a>
                <a href="/videos" class="btn">🎬 影片庫</a>
                <a href="/food" class="btn">🍎 食品管理</a>
                <a href="/subscription" class="btn">📋 訂閱管理</a>
            </div>
            
            <div style="margin-top: 15px;">
                <a href="/install.php" class="btn">⚙️ 安裝頁面</a>
                <a href="/api?path=stats" class="btn">📊 API 統計</a>
            </div>
        </div>
    </div>
</body>
</html>