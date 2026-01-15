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
        <span style="font-size: 12px; opacity: 0.7;">示例內容</span>
    </div>
    <div class="media-grid">
        <?php
        $items = [];
        for ($i = 1; $i <= 8; $i++) {
            $emoji = ['🎬','🎥','📺','🎞️','📹','🍿','⭐','🎧'][($i-1)%8];
            $svg = rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='400' height='400'><rect width='100%' height='100%' fill='#1f2937'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='96' fill='#ffffff'>".$emoji."</text></svg>");
            $items[] = [
                'title' => '示例影片 ' . $i,
                'src' => "data:image/svg+xml;charset=utf-8," . $svg
            ];
        }
        ?>
        <?php foreach ($items as $v): ?>
        <div class="media-item">
            <img src="<?= $v['src'] ?>" alt="<?= htmlspecialchars($v['title']) ?>">
            <div class="media-overlay">
                <div class="media-title"><?= htmlspecialchars($v['title']) ?></div>
                <div class="media-info">示意圖</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
