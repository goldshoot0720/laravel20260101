<?php
$current_page = 'dashboard';
$title = '系統儀表板 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🏠</span>
        系統儀表板
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">即時監控和管理各項資訊</small>
    </h1>
    <a href="#" class="btn btn-primary">刷新數據</a>
</div>

<!-- 系統概覽 -->
<div class="card fade-in">
    <div class="card-header">
        <h2 class="card-title">🔥 鋒兄塗哥公關資訊</h2>
        <span style="font-size: 14px; opacity: 0.7;">智能管理您的影片和圖片收藏，支援智能分類和快速搜尋</span>
    </div>
    
    <div style="text-align: center; padding: 40px 0;">
        <div style="font-size: 48px; margin-bottom: 20px;">🔥</div>
        <h3 style="margin-bottom: 10px;">鋒兄塗哥公關資訊</h3>
        <p style="opacity: 0.8; margin-bottom: 30px;">智能管理您的食品和訂閱服務，支援智能分類和快速搜尋</p>
        
        <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 12px; margin: 20px 0;">
            <p style="margin-bottom: 15px;">鋒兄塗哥公關資訊 © 版權所有 2025 - 2125</p>
            
            <div class="grid grid-2" style="margin-top: 20px;">
                <div>
                    <h4 style="color: #fbbf24; margin-bottom: 10px;">🛠️ 前端技術</h4>
                    <ul style="list-style: none; text-align: left;">
                        <li>• 原生 JavaScript</li>
                        <li>• CSS / 響應式設計</li>
                        <li>• 可接軌 React (Next.js)</li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: #f472b6; margin-bottom: 10px;">💎 後端與資料庫</h4>
                    <ul style="list-style: none; text-align: left;">
                        <li>• PHP (Laravel)</li>
                        <li>• MySQL</li>
                        <li>• RESTful API</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 30px;">
            <h4 style="color: #fbbf24;">⭐ 系統功能選單</h4>
        </div>
    </div>
</div>

<!-- 功能統計 -->
<div class="grid grid-2">
    <div class="card stat-card fade-in">
        <div class="stat-number stat-warning">15</div>
        <div class="stat-label">食品項目</div>
    </div>
    <div class="card stat-card fade-in">
        <div class="stat-number stat-error">2</div>
        <div class="stat-label">活躍訂閱</div>
    </div>
</div>

<!-- 快速操作 -->
<div class="grid grid-2">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">📊 訂閱管理</h3>
        </div>
        <div class="grid grid-4" style="text-align: center;">
            <div>
                <div class="stat-number" style="font-size: 24px; color: #6366f1;">24</div>
                <div class="stat-label">總計訂閱</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #ef4444;">0</div>
                <div class="stat-label">3天內到期</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #f59e0b;">1</div>
                <div class="stat-label">7天內到期</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #6b7280;">0</div>
                <div class="stat-label">已過期</div>
            </div>
        </div>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 10px; color: #fbbf24;">
                <span>⚠️</span>
                <span style="font-size: 14px;">訂閱到期提醒</span>
            </div>
        </div>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">🍎 食品管理</h3>
        </div>
        <div class="grid grid-4" style="text-align: center;">
            <div>
                <div class="stat-number" style="font-size: 24px; color: #10b981;">15</div>
                <div class="stat-label">總食品數</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #ef4444;">0</div>
                <div class="stat-label">3天內到期</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #f59e0b;">0</div>
                <div class="stat-label">7天內到期</div>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; color: #f59e0b;">2</div>
                <div class="stat-label">30天內到期</div>
            </div>
        </div>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 10px; color: #ef4444;">
                <span>🍎</span>
                <span style="font-size: 14px;">食品到期提醒</span>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
