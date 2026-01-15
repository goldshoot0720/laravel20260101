<?php
$current_page = 'gallery';
$title = '首頁 - ' . SYSTEM_NAME;

$dirCandidates = [
    __DIR__ . '/../uploads/images',
    __DIR__ . '/../uploads',
];

$imageDir = null;
foreach ($dirCandidates as $d) {
    if (is_dir($d)) {
        $imageDir = $d;
        break;
    }
}

$files = [];
if ($imageDir) {
    $files = glob($imageDir . '/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE) ?: [];
}

$jpgCount = 0;
$pngCount = 0;
foreach ($files as $f) {
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if ($ext === 'jpg' || $ext === 'jpeg') $jpgCount++;
    if ($ext === 'png') $pngCount++;
}
$totalCount = count($files);

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🏠</span>
        首頁
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">圖片展示與統計</small>
    </h1>
    <a href="/" class="btn btn-primary">重新載入</a>
    </div>

<div class="card fade-in" style="background: linear-gradient(135deg,#4f46e5,#9333ea); color: white;">
    <div class="card-header">
        <h2 class="card-title" style="color: white;">鋒兄塗哥公關資訊</h2>
        <span style="font-size: 14px; opacity: 0.9;">前端使用 原生 JavaScript；後端使用 PHP (Laravel)；資料庫使用 MySQL</span>
    </div>
    <div style="display:flex; justify-content:flex-end;">
        <button class="btn" style="background: rgba(255,255,255,0.2); color: white;">匯新蒐入</button>
    </div>
</div>

<div class="grid grid-3">
    <div class="card stat-card fade-in" style="background: #0ea5e9;">
        <div class="stat-number" style="color: white;"><?= $totalCount ?></div>
        <div class="stat-label" style="color: white;">總圖片數</div>
    </div>
    <div class="card stat-card fade-in" style="background: #22c55e;">
        <div class="stat-number" style="color: white;"><?= $jpgCount ?></div>
        <div class="stat-label" style="color: white;">JPG/JPEG</div>
    </div>
    <div class="card stat-card fade-in" style="background: #a855f7;">
        <div class="stat-number" style="color: white;"><?= $pngCount ?></div>
        <div class="stat-label" style="color: white;">PNG</div>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h3 class="card-title">圖片展示</h3>
        <span style="font-size: 12px; opacity: 0.7;">共 <?= $totalCount ?> 張</span>
    </div>
    <div class="media-grid">
        <?php if ($totalCount > 0): ?>
            <?php foreach ($files as $f): 
                $rel = str_replace(realpath(__DIR__ . '/..'), '', realpath($f));
                $rel = str_replace('\\', '/', $rel);
                $name = basename($f);
                $size = filesize($f);
                $kb = $size ? round($size / 1024, 1) : 0;
            ?>
            <div class="media-item">
                <img src="<?= $rel ?>" alt="<?= htmlspecialchars($name) ?>">
                <div class="media-overlay">
                    <div class="media-title"><?= htmlspecialchars($name) ?></div>
                    <div class="media-info"><?= $kb ?> KB</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php
            $placeholders = [];
            for ($i = 1; $i <= 12; $i++) {
                $placeholders[] = [
                    'title' => '示例圖片 ' . $i,
                    'bg1' => ['#4f46e5','#22c55e','#a855f7','#0ea5e9'][($i-1)%4],
                    'emoji' => ['🧑‍🎨','🧑‍💻','🎨','🎬','🎵','📷','🖼️','🤖'][($i-1)%8],
                ];
            }
            ?>
            <?php foreach ($placeholders as $p): 
                $svg = rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='400' height='400'><rect width='100%' height='100%' fill='{$p['bg1']}'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='96'>".$p['emoji']."</text></svg>");
                $src = "data:image/svg+xml;charset=utf-8," . $svg;
            ?>
            <div class="media-item">
                <img src="<?= $src ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                <div class="media-overlay">
                    <div class="media-title"><?= htmlspecialchars($p['title']) ?></div>
                    <div class="media-info">示意圖</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
