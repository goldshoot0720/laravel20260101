<?php
$current_page = 'subscription';
$title = '訂閱管理系統 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>📋</span>
        訂閱管理系統
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">智能的合理訂閱服務和會員資訊</small>
    </h1>
    <div style="display: flex; gap: 10px;">
        <a href="#" class="btn btn-success">新增訂閱</a>
        <a href="#" class="btn btn-primary">重新載入</a>
    </div>
</div>

<!-- 搜尋和篩選 -->
<div class="card fade-in">
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <div class="search-box" style="flex: 1; min-width: 300px; margin-bottom: 0;">
            <input type="text" class="search-input" placeholder="搜尋訂閱名稱...">
            <button class="search-btn">🔍</button>
        </div>
        <button class="btn btn-primary">搜尋</button>
        <button class="btn btn-primary">重新載入</button>
    </div>
</div>

<!-- 訂閱項目 -->
<div class="grid grid-1">
    <!-- 訂閱項目 1 -->
    <div class="card fade-in">
        <div style="padding: 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(255,255,255,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin-bottom: 5px;">天虎/黃信訊/心臟內科</h3>
                    <div style="font-size: 14px; opacity: 0.7;">
                        網站: https://www.tcmg.com.tw/index.php/main/schedule_time?id=18
                    </div>
                </div>
                <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">即將到期</span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">價格</div>
                    <div style="font-weight: 600;">NT$ 530</div>
                </div>
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">下次付款</div>
                    <div style="font-weight: 600;">2025-12-26</div>
                </div>
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">剩餘天數</div>
                    <div style="font-weight: 600; color: #f59e0b;">1 天</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn btn-primary">編輯</button>
                <button class="btn btn-error">刪除</button>
            </div>
        </div>
    </div>

    <!-- 訂閱項目 2 -->
    <div class="card fade-in">
        <div style="padding: 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(255,255,255,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin-bottom: 5px;">kiro pro</h3>
                    <div style="font-size: 14px; opacity: 0.7;">
                        網站: https://app.kiro.dev/account/
                    </div>
                </div>
                <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">正常</span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">價格</div>
                    <div style="font-weight: 600;">NT$ 640</div>
                </div>
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">下次付款</div>
                    <div style="font-weight: 600;">2026-01-01</div>
                </div>
                <div>
                    <div style="font-size: 12px; opacity: 0.6;">剩餘天數</div>
                    <div style="font-weight: 600; color: #10b981;">10 天</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn btn-primary">編輯</button>
                <button class="btn btn-error">刪除</button>
            </div>
        </div>
    </div>
</div>

<!-- 更多訂閱項目 -->
<div class="grid grid-2">
    <?php 
    $subscriptions = [
        ['name' => 'Netflix', 'price' => 390, 'days' => 15, 'status' => 'success', 'icon' => '🎬'],
        ['name' => 'Spotify', 'price' => 149, 'days' => 8, 'status' => 'warning', 'icon' => '🎵'],
        ['name' => 'Adobe Creative', 'price' => 1680, 'days' => 22, 'status' => 'success', 'icon' => '🎨'],
        ['name' => 'Microsoft 365', 'price' => 315, 'days' => 5, 'status' => 'warning', 'icon' => '💼'],
        ['name' => 'GitHub Pro', 'price' => 120, 'days' => 18, 'status' => 'success', 'icon' => '💻'],
        ['name' => 'Dropbox Plus', 'price' => 330, 'days' => 2, 'status' => 'error', 'icon' => '☁️'],
        ['name' => 'YouTube Premium', 'price' => 179, 'days' => 12, 'status' => 'success', 'icon' => '📺'],
        ['name' => 'Notion Pro', 'price' => 240, 'days' => 25, 'status' => 'success', 'icon' => '📝'],
        ['name' => 'Figma Pro', 'price' => 480, 'days' => 7, 'status' => 'warning', 'icon' => '🎯'],
        ['name' => 'ChatGPT Plus', 'price' => 600, 'days' => 20, 'status' => 'success', 'icon' => '🤖'],
        ['name' => 'Canva Pro', 'price' => 450, 'days' => 3, 'status' => 'error', 'icon' => '🖼️'],
        ['name' => 'Zoom Pro', 'price' => 540, 'days' => 16, 'status' => 'success', 'icon' => '📹'],
        ['name' => 'Slack Pro', 'price' => 270, 'days' => 9, 'status' => 'warning', 'icon' => '💬'],
        ['name' => 'Trello Gold', 'price' => 150, 'days' => 28, 'status' => 'success', 'icon' => '📊'],
        ['name' => 'LastPass Premium', 'price' => 108, 'days' => 4, 'status' => 'warning', 'icon' => '🔐'],
        ['name' => 'Evernote Premium', 'price' => 240, 'days' => 13, 'status' => 'success', 'icon' => '📚'],
        ['name' => 'Grammarly Premium', 'price' => 360, 'days' => 6, 'status' => 'warning', 'icon' => '✍️'],
        ['name' => 'Todoist Pro', 'price' => 120, 'days' => 19, 'status' => 'success', 'icon' => '✅'],
        ['name' => 'NordVPN', 'price' => 99, 'days' => 11, 'status' => 'success', 'icon' => '🛡️'],
        ['name' => 'Mailchimp Pro', 'price' => 300, 'days' => 1, 'status' => 'error', 'icon' => '📧'],
    ];
    
    foreach($subscriptions as $sub):
        $statusColor = [
            'success' => '#10b981',
            'warning' => '#f59e0b', 
            'error' => '#ef4444'
        ][$sub['status']];
        
        $statusText = [
            'success' => '正常',
            'warning' => '即將到期',
            'error' => '緊急'
        ][$sub['status']];
    ?>
    <div class="card fade-in" style="padding: 15px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
            <div style="font-size: 24px;"><?= $sub['icon'] ?></div>
            <div style="flex: 1;">
                <h4 style="margin-bottom: 2px;"><?= $sub['name'] ?></h4>
                <div style="font-size: 12px; opacity: 0.6;">NT$ <?= $sub['price'] ?> / 月</div>
            </div>
            <span style="background: <?= $statusColor ?>; color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px;">
                <?= $statusText ?>
            </span>
        </div>
        <div style="font-size: 12px; opacity: 0.7; margin-bottom: 10px;">
            剩餘天數: <span style="color: <?= $statusColor ?>; font-weight: 600;"><?= $sub['days'] ?> 天</span>
        </div>
        <div style="display: flex; gap: 6px;">
            <button class="btn btn-primary" style="font-size: 10px; padding: 4px 8px; flex: 1;">編輯</button>
            <button class="btn btn-error" style="font-size: 10px; padding: 4px 8px;">刪除</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>