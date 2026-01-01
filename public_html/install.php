<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>鋒兄AI資訊系統 - 安裝程序</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', 'PingFang TC', 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .step {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .step-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-content {
            font-size: 14px;
            line-height: 1.6;
        }
        .status {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-success { background: #10b981; }
        .status-error { background: #ef4444; }
        .status-warning { background: #f59e0b; }
        .status-info { background: #3b82f6; }
        .btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #5856eb;
        }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
        .code {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .alert-info {
            background: rgba(59, 130, 246, 0.2);
            border-left: 4px solid #3b82f6;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.2);
            border-left: 4px solid #f59e0b;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border-left: 4px solid #10b981;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔥</div>
            <h1>鋒兄AI資訊系統</h1>
            <p>資料庫安裝與初始化程序</p>
        </div>

        <?php
        // 檢查是否已執行安裝
        $installLockFile = 'install.lock';
        $forceInstall = isset($_GET['force']) && $_GET['force'] === 'true';
        
        if (file_exists($installLockFile) && !$forceInstall) {
            echo '<div class="alert alert-warning">';
            echo '<h3>⚠️ 系統已安裝</h3>';
            echo '<p>系統已經完成安裝。如果需要重新安裝，請在網址後加上 <code>?force=true</code></p>';
            echo '<p><a href="/" class="btn btn-success">前往系統首頁</a></p>';
            echo '</div>';
        } else {
            // 執行安裝程序
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
                performInstallation();
            } else {
                showInstallationForm();
            }
        }
        
        function showInstallationForm() {
            ?>
            <div class="step">
                <div class="step-title">
                    <span>📋</span>
                    <span>安裝前檢查</span>
                </div>
                <div class="step-content">
                    <?php
                    $checks = [
                        'PHP版本' => version_compare(PHP_VERSION, '7.4.0', '>='),
                        'PDO擴展' => extension_loaded('pdo'),
                        'PDO MySQL' => extension_loaded('pdo_mysql'),
                        'JSON擴展' => extension_loaded('json'),
                        '寫入權限' => is_writable(__DIR__)
                    ];
                    
                    $allPassed = true;
                    foreach ($checks as $check => $passed) {
                        $status = $passed ? 'success' : 'error';
                        $icon = $passed ? '✅' : '❌';
                        echo "<p>{$icon} {$check}: <span class='status status-{$status}'>" . ($passed ? '通過' : '失敗') . "</span></p>";
                        if (!$passed) $allPassed = false;
                    }
                    ?>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span>🗄️</span>
                    <span>資料庫配置</span>
                </div>
                <div class="step-content">
                    <p>系統將根據環境自動選擇資料庫配置：</p>
                    <div class="code">
本地測試環境:
- 主機: localhost
- 用戶: root
- 密碼: (空白)
- 資料庫: feng_laravel

遠端上線環境:
- 主機: localhost  
- 用戶: feng_laravel
- 密碼: ym0Tagood129
- 資料庫: feng_laravel
                    </div>
                    
                    <?php
                    // 測試資料庫連接
                    try {
                        require_once 'config/database.php';
                        $connected = testDatabaseConnection();
                        
                        if ($connected) {
                            echo '<div class="alert alert-success">';
                            echo '<h4>✅ 資料庫連接成功</h4>';
                            echo '<p>環境: <strong>' . DB_ENVIRONMENT . '</strong></p>';
                            echo '<p>資料庫: <strong>' . DB_DATABASE . '</strong></p>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-error">';
                            echo '<h4>❌ 資料庫連接失敗</h4>';
                            echo '<p>請檢查資料庫配置和權限</p>';
                            echo '</div>';
                            $allPassed = false;
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-error">';
                        echo '<h4>❌ 資料庫連接錯誤</h4>';
                        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                        $allPassed = false;
                    }
                    ?>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span>🚀</span>
                    <span>開始安裝</span>
                </div>
                <div class="step-content">
                    <?php if ($allPassed): ?>
                        <p>所有檢查都已通過，可以開始安裝系統。</p>
                        <div class="alert alert-info">
                            <h4>安裝將執行以下操作：</h4>
                            <ul>
                                <li>創建資料庫表結構</li>
                                <li>插入初始數據</li>
                                <li>設置系統配置</li>
                                <li>創建安裝鎖定文件</li>
                            </ul>
                        </div>
                        <form method="post">
                            <button type="submit" name="install" class="btn btn-success">開始安裝系統</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <h4>❌ 安裝條件不滿足</h4>
                            <p>請解決上述問題後再進行安裝。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        
        function performInstallation() {
            echo '<div class="step">';
            echo '<div class="step-title"><span>⚙️</span><span>正在安裝...</span></div>';
            echo '<div class="step-content">';
            
            try {
                // 執行資料庫遷移
                require_once 'database/migrations.php';
                
                echo '<p>🔄 正在創建資料庫表...</p>';
                $migration = new DatabaseMigration();
                $migration->runMigrations();
                
                echo '<p>🔄 正在插入初始數據...</p>';
                $migration->seedData();
                
                echo '<p>🔄 正在創建安裝鎖定文件...</p>';
                file_put_contents('install.lock', date('Y-m-d H:i:s'));
                
                echo '<div class="alert alert-success">';
                echo '<h3>🎉 安裝完成！</h3>';
                echo '<p>鋒兄AI資訊系統已成功安裝並初始化。</p>';
                echo '<p>環境: <strong>' . DB_ENVIRONMENT . '</strong></p>';
                echo '<p>資料庫: <strong>' . DB_DATABASE . '</strong></p>';
                echo '<p><a href="/" class="btn btn-success">前往系統首頁</a></p>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-error">';
                echo '<h3>❌ 安裝失敗</h3>';
                echo '<p>錯誤信息: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p><a href="?force=true" class="btn">重新安裝</a></p>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div class="step">
            <div class="step-title">
                <span>📚</span>
                <span>說明文檔</span>
            </div>
            <div class="step-content">
                <p>安裝完成後，您可以：</p>
                <ul>
                    <li>訪問系統首頁開始使用各項功能</li>
                    <li>查看 <code>README.md</code> 了解詳細使用說明</li>
                    <li>通過 API 接口進行數據操作</li>
                    <li>自定義系統配置和樣式</li>
                </ul>
                
                <div class="alert alert-info">
                    <h4>技術支援</h4>
                    <p>如遇到問題，請檢查：</p>
                    <ul>
                        <li>PHP 錯誤日誌</li>
                        <li>資料庫連接配置</li>
                        <li>文件權限設置</li>
                        <li>Web 服務器配置</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>