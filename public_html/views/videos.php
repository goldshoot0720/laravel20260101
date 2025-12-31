<?php
$current_page = 'videos';
$title = '鋒兄影片庫 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🎬</span>
        鋒兄影片庫
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">智能的精美人生與世界歷程</small>
    </h1>
    <a href="#" class="btn btn-primary">新增影片</a>
</div>

<!-- 搜尋框 -->
<div class="search-box fade-in">
    <input type="text" class="search-input" placeholder="搜尋影片名稱...">
    <button class="search-btn">🔍</button>
</div>

<!-- 影片列表 -->
<div class="grid grid-2">
    <!-- 影片項目 1 -->
    <div class="card fade-in">
        <div style="position: relative; border-radius: 12px; overflow: hidden; margin-bottom: 15px;">
            <div style="aspect-ratio: 16/9; background: linear-gradient(45deg, #ff6b35, #f7931e); display: flex; align-items: center; justify-content: center; position: relative;">
                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                    MP4
                </div>
                <div style="text-align: center; color: white;">
                    <div style="font-size: 48px; margin-bottom: 10px;">🎬</div>
                    <div style="font-size: 14px; opacity: 0.9;">鋒兄的傳奇人生</div>
                </div>
                <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                    00:45
                </div>
            </div>
        </div>
        <h3 style="margin-bottom: 8px;">鋒兄的傳奇人生</h3>
        <p style="font-size: 14px; opacity: 0.7; margin-bottom: 15px;">鋒兄人生歷程紀錄片</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; opacity: 0.6;">
            <span>大小: 2.01 MB</span>
            <span>時長: 00:45</span>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button class="btn btn-primary" style="flex: 1;">播放</button>
            <button class="btn btn-error">刪除</button>
        </div>
    </div>

    <!-- 影片項目 2 -->
    <div class="card fade-in">
        <div style="position: relative; border-radius: 12px; overflow: hidden; margin-bottom: 15px;">
            <div style="aspect-ratio: 16/9; background: linear-gradient(45deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; position: relative;">
                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                    MP4
                </div>
                <div style="text-align: center; color: white;">
                    <div style="font-size: 48px; margin-bottom: 10px;">🎭</div>
                    <div style="font-size: 14px; opacity: 0.9;">鋒兄進化Show</div>
                </div>
                <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                    01:23
                </div>
            </div>
        </div>
        <h3 style="margin-bottom: 8px;">鋒兄進化Show 🔥</h3>
        <p style="font-size: 14px; opacity: 0.7; margin-bottom: 15px;">鋒兄進化歷程山歷程</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; opacity: 0.6;">
            <span>大小: 4.21 MB</span>
            <span>時長: 01:23</span>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button class="btn btn-primary" style="flex: 1;">播放</button>
            <button class="btn btn-error">刪除</button>
        </div>
    </div>
</div>

<!-- 空狀態提示 -->
<div class="card fade-in" style="text-align: center; padding: 60px 20px;">
    <div style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;">🎬</div>
    <h3 style="margin-bottom: 10px; opacity: 0.8;">影片庫內容豐富</h3>
    <p style="opacity: 0.6; margin-bottom: 30px;">目前共有 2 部精彩影片，記錄著鋒兄的人生歷程</p>
    <button class="btn btn-primary">上傳新影片</button>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>