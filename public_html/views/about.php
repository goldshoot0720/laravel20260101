<?php
$current_page = 'about';
$title = '關於我們 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>ℹ️</span>
        關於我們
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">了解鋒兄塗哥公關資訊的使命與願景</small>
    </h1>
    <a href="/about" class="btn btn-primary">重新載入</a>
</div>

<div class="hero-card fade-in">
    <div class="hero-title">鋒兄塗哥公關資訊</div>
    <div class="hero-sub">我們是專業的公關團隊，致力於提供優質的公關服務和智慧管理解決方案。</div>
</div>

<div class="grid grid-2">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">鋒</h3>
        </div>
        <p style="opacity:.85;">
            技術整合 & 創新研發。專注於系統設計、資料整合與工程落地，將技術用於提升管理效率。
        </p>
    </div>
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">塗哥</h3>
        </div>
        <p style="opacity:.85;">
            公關顧問 & 內容專家。擅長整合跨領域資源，提升品牌影響力，連結企業與受眾之間的信任。
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
