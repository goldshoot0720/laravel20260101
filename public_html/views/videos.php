<?php
$current_page = 'videos';
$title = '影片介紹 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🎬</span>
        影片介紹
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">示範佈局與媒體網格</small>
    </h1>
    <a href="/videos" class="btn btn-primary">重新載入</a>
    </div>

<div class="card fade-in">
    <div class="card-header">
        <h3 class="card-title">影片清單</h3>
        <span style="font-size: 12px; opacity: 0.7;">來源：/videos</span>
    </div>
    <div class="media-grid">
        <?php
        $videoDir = __DIR__ . '/../videos';
        $files = [];
        if (is_dir($videoDir)) {
            $files = glob($videoDir . '/*.{mp4,webm,mov,MP4,WEBM,MOV}', GLOB_BRACE) ?: [];
        }
        if (!empty($files)):
            foreach ($files as $f):
                $rel = str_replace(realpath(__DIR__ . '/..'), '', realpath($f));
                $rel = str_replace('\\', '/', $rel);
                $name = basename($f);
                $size = filesize($f);
                $mb = $size ? round($size / 1024 / 1024, 2) : 0;
        ?>
        <div class="media-item video-card">
            <div class="video-thumb">
                <video src="<?= $rel ?>" preload="metadata" muted playsinline></video>
                <span class="duration-pill video-duration">--:--</span>
                <button class="play-btn">▶</button>
            </div>
            <div class="image-meta">
                <div class="media-title"><?= htmlspecialchars($name) ?></div>
                <div class="media-info"><?= $mb ?> MB</div>
            </div>
        </div>
        <?php
            endforeach;
        else:
            for ($i = 1; $i <= 8; $i++):
                $emoji = ['🎬','🎥','📺','🎞️','📹','🍿','⭐','🎧'][($i-1)%8];
                $svg = rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='400' height='225'><rect width='100%' height='100%' fill='#1f2937'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='96' fill='#ffffff'>".$emoji."</text></svg>");
                $src = "data:image/svg+xml;charset=utf-8," . $svg;
        ?>
        <div class="media-item video-card">
            <div class="video-thumb">
                <img src="<?= $src ?>" alt="示例影片">
                <span class="duration-pill">--:--</span>
                <button class="play-btn">▶</button>
            </div>
            <div class="image-meta">
                <div class="media-title">示例影片 <?= $i ?></div>
                <div class="media-info">示意圖</div>
            </div>
        </div>
        <?php endfor; endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
