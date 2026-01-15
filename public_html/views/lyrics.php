<?php
$current_page = 'lyrics';
$title = '鋒兄音樂歌詞 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🎵</span>
        鋒兄音樂歌詞
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">收藏與管理常愛的歌曲歌詞</small>
    </h1>
    <a href="/lyrics" class="btn btn-primary">重新載入</a>
</div>

<div class="grid grid-2">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">歌曲庫</h3>
            <span style="font-size: 12px; opacity: 0.7;">來源：/musics</span>
        </div>
        <div class="search-box">
            <input type="text" class="search-input" placeholder="搜尋歌曲、歌手或歌詞片段...">
            <button class="search-btn">🔍</button>
        </div>
        <div class="media-grid">
            <?php
            $musicDir = __DIR__ . '/../musics';
            $files = [];
            if (is_dir($musicDir)) {
                $files = glob($musicDir . '/*.{mp3,wav,m4a,MP3,WAV,M4A}', GLOB_BRACE) ?: [];
            }
            if (!empty($files)):
                foreach ($files as $f):
                    $rel = str_replace(realpath(__DIR__ . '/..'), '', realpath($f));
                    $rel = str_replace('\\', '/', $rel);
                    $name = basename($f);
                    $size = filesize($f);
                    $mb = $size ? round($size / 1024 / 1024, 2) : 0;
            ?>
            <div class="media-item audio-card">
                <div class="audio-thumb">
                    <audio src="<?= $rel ?>" preload="metadata"></audio>
                    <div class="audio-art">🎵</div>
                    <span class="duration-pill audio-duration">--:--</span>
                </div>
                <div class="image-meta">
                    <div class="media-title"><?= htmlspecialchars($name) ?></div>
                    <div class="media-info"><?= $mb ?> MB</div>
                </div>
            </div>
            <?php
                endforeach;
            else:
                $placeholders = ['鋒兄進化Show','塗哥水電王子爆紅','婚禮經典'];
                foreach ($placeholders as $i => $title):
            ?>
            <div class="media-item audio-card">
                <div class="audio-thumb">
                    <div class="audio-art">🎵</div>
                    <span class="duration-pill">--:--</span>
                </div>
                <div class="image-meta">
                    <div class="media-title"><?= htmlspecialchars($title) ?></div>
                    <div class="media-info">示意音檔</div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">歌曲內容</h3>
            <span style="font-size: 12px; opacity: 0.7;">切換語言與版本</span>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <?php foreach (['中文','English','日文','繁體','韓文'] as $lang): ?>
            <button class="btn" style="background:#f3f4f6; color:#111;"><?= $lang ?></button>
            <?php endforeach; ?>
        </div>
        <div class="card" id="lyricsPlayer">
            <h4 id="lyricsTitle" style="margin-bottom:6px;">選擇左側歌曲開始播放</h4>
            <audio id="lyricsAudio" controls style="width:100%;"></audio>
            <div class="progress-row" style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <span id="lyricsCurrent" style="font-size:12px; opacity:.8;">0:00</span>
                <div id="lyricsProgress" class="progress">
                    <div id="lyricsProgressFill" class="progress-fill" style="width:0%;"></div>
                </div>
                <span id="lyricsDuration" style="font-size:12px; opacity:.8;">0:00</span>
            </div>
            <div id="lyricsText" class="lyrics-text" style="margin-top:12px;">
                西元兩零零四年六月十五日，這一天是國中畢業生可以在畢業紀念冊留下紀念簽名的一天。<br>
                我們同時也是商店的某某店員，鋒兄一位人稱超哥一切的開始。
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
