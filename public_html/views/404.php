<?php
$title = '頁面未找到 - ' . SYSTEM_NAME;

ob_start();
?>

<div style="text-align: center; padding: 100px 20px; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div style="font-size: 120px; margin-bottom: 30px; opacity: 0.5;">🔍</div>
    <h1 style="font-size: 48px; margin-bottom: 20px; color: #ef4444;">404</h1>
    <h2 style="font-size: 24px; margin-bottom: 15px;">頁面未找到</h2>
    <p style="font-size: 16px; opacity: 0.7; margin-bottom: 40px; max-width: 500px;">
        抱歉，您要尋找的頁面不存在。可能是網址輸入錯誤，或者頁面已被移動或刪除。
    </p>
    
    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
        <a href="/" class="btn btn-primary">返回首頁</a>
        <a href="/gallery" class="btn btn-secondary" style="background: rgba(255,255,255,0.1);">圖片庫</a>
        <a href="/videos" class="btn btn-secondary" style="background: rgba(255,255,255,0.1);">影片庫</a>
    </div>
    
    <div style="margin-top: 50px; font-size: 14px; opacity: 0.5;">
        <p>如果問題持續存在，請聯繫系統管理員</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>