@echo off
echo 🔥 鋒兄AI資訊系統 - 本地開發服務器
echo =====================================
echo.
echo 正在啟動服務器...
cd public_html
echo.
echo 🌐 服務器地址:
echo    首頁: http://127.0.0.1:8888/
echo    狀態: http://127.0.0.1:8888/system-status.php
echo    安裝: http://127.0.0.1:8888/install.php
echo.
echo 📱 功能頁面:
echo    圖片庫: http://127.0.0.1:8888/gallery
echo    影片庫: http://127.0.0.1:8888/videos
echo    食品管理: http://127.0.0.1:8888/food
echo    訂閱管理: http://127.0.0.1:8888/subscription
echo.
echo 🔌 API 接口:
echo    統計: http://127.0.0.1:8888/api?path=stats
echo.
echo 按 Ctrl+C 停止服務器
echo.
php -S 127.0.0.1:8888 router.php
pause