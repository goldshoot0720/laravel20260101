<?php
$current_page = 'bank';
$title = '銀行統計 - ' . SYSTEM_NAME;

// 範例銀行資料
$banks = [
    ['name' => '台北富邦', 'value' => 423],
    ['name' => '玉山銀行', 'value' => 496],
    ['name' => '國泰世華', 'value' => 213],
    ['name' => '兆豐銀行', 'value' => 452],
    ['name' => '王道銀行', 'value' => 500],
    ['name' => '新光銀行', 'value' => 200],
    ['name' => '中華郵政', 'value' => 601],
    ['name' => '中國信託', 'value' => 696],
    ['name' => '台新銀行', 'value' => 611],
];
$sum = array_sum(array_column($banks,'value'));
$count = count($banks);
$editable = '是';

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🏦</span>
        銀行統計
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">金融帳目統計與分析</small>
    </h1>
    <a href="/bank" class="btn btn-primary">重新載入</a>
</div>

<div class="hero-card fade-in">
    <div class="hero-title">鋒兄銀行統計</div>
    <div class="hero-sub">彙整各銀行帳目資料，供快速檢視與管理</div>
    <div class="stat-row">
        <div class="stat-chip chip-violet">
            <div>
                <div class="label">合計</div>
                <div class="value"><?= $sum ?></div>
            </div>
            <div class="icon">💜</div>
        </div>
        <div class="stat-chip chip-blue">
            <div>
                <div class="label">銀行數</div>
                <div class="value"><?= $count ?></div>
            </div>
            <div class="icon">🏦</div>
        </div>
        <div class="stat-chip chip-green">
            <div>
                <div class="label">可編輯</div>
                <div class="value"><?= $editable ?></div>
            </div>
            <div class="icon">✅</div>
        </div>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h3 class="card-title">銀行列表</h3>
        <span style="font-size: 12px; opacity: 0.7;">快速檢視每家銀行的統計數</span>
    </div>
    <div class="grid grid-3">
        <?php foreach ($banks as $b): ?>
        <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span>🏦</span>
                <strong><?= htmlspecialchars($b['name']) ?></strong>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="background:#f3f4f6; color:#111; border-radius:10px; padding:6px 10px; font-weight:700;"><?= $b['value'] ?></span>
                <button class="btn" style="background:#f3f4f6; color:#111;">詳細</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
