@echo off
echo 🔥 鋒兄AI資訊系統 - 系統測試
echo =============================
echo.
echo 測試資料庫連接...
cd public_html
php -r "
require_once 'config/database.php';
try {
    if (testDatabaseConnection()) {
        echo '✅ 資料庫連接成功\n';
        echo '環境: ' . DB_ENVIRONMENT . '\n';
        echo '資料庫: ' . DB_DATABASE . '\n';
    } else {
        echo '❌ 資料庫連接失敗\n';
    }
} catch (Exception \$e) {
    echo '❌ 錯誤: ' . \$e->getMessage() . '\n';
}
"
echo.
echo 測試完成！
echo.
echo 要啟動開發服務器嗎？(Y/N)
set /p choice=
if /i "%choice%"=="Y" (
    echo 啟動服務器於 http://127.0.0.1:9000
    php -S 127.0.0.1:9000
)
pause