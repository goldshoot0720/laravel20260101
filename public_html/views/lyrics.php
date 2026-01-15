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
                    $lang = 'zh';
                    if (stripos($name, 'English') !== false || mb_strpos($name, '英') !== false) $lang = 'en';
                    elseif (mb_strpos($name, '日') !== false) $lang = 'ja';
                    elseif (mb_strpos($name, '韓') !== false) $lang = 'ko';
                    elseif (mb_strpos($name, '粵') !== false) $lang = 'yue';
                    $langLabel = ['zh'=>'中','en'=>'英','ja'=>'日','ko'=>'韓','yue'=>'粵'][$lang];
                    $titlecat = 'other';
                    if (mb_strpos($name, '鋒兄進化Show') !== false) $titlecat = 'show';
                    elseif (mb_strpos($name, '鋒兄的傳奇人生') !== false) $titlecat = 'legend';
                    elseif (mb_strpos($name, '最瞎結婚理由') !== false) $titlecat = 'marriage';
                    elseif (mb_strpos($name, '塗哥水電王子爆紅') !== false) $titlecat = 'tuge';
                    $variant = 'none';
                    if (mb_strpos($name, '(Pekora)') !== false) $variant = 'pekora';
                    elseif (mb_strpos($name, '(Donald Trump)') !== false) $variant = 'trump';
                    elseif (mb_strpos($name, '(SpongeBob SquarePants)') !== false) $variant = 'spongebob';
                    elseif (mb_strpos($name, '(Sidhu)') !== false) $variant = 'sidhu';
                    elseif (mb_strpos($name, '(Rose)') !== false) $variant = 'rose';
                    elseif (mb_strpos($name, '(Freddie Mercury)') !== false) $variant = 'freddie';
                    elseif (mb_strpos($name, '(Hatsune Miku)') !== false) $variant = 'miku';
                    $titleLabel = [
                        'show'=>'鋒兄進化Show🔥',
                        'legend'=>'鋒兄的傳奇人生',
                        'marriage'=>'最瞎結婚理由',
                        'tuge'=>'塗哥水電王子爆紅',
                        'other'=>'其他'
                    ][$titlecat];
                    $variantLabel = [
                        'pekora'=>'Pekora','trump'=>'Donald Trump','spongebob'=>'SpongeBob SquarePants',
                        'sidhu'=>'Sidhu','rose'=>'Rose','freddie'=>'Freddie Mercury','miku'=>'Hatsune Miku','none'=>'原版'
                    ][$variant];
            ?>
            <div class="media-item audio-card" data-lang="<?= $lang ?>" data-title="<?= $titlecat ?>" data-variant="<?= $variant ?>">
                <div class="audio-thumb">
                    <audio src="<?= $rel ?>" preload="metadata"></audio>
                    <div class="audio-art">🎵</div>
                    <span class="duration-pill audio-duration">--:--</span>
                </div>
                <div class="image-meta">
                    <div class="media-title"><?= htmlspecialchars($name) ?></div>
                    <div class="media-info"><?= $mb ?> MB · <?= $langLabel ?> · <?= $titleLabel ?> · <?= $variantLabel ?></div>
                </div>
            </div>
            <?php
                endforeach;
            else:
                $placeholders = ['鋒兄進化Show','塗哥水電王子爆紅','婚禮經典'];
                foreach ($placeholders as $i => $title):
            ?>
            <div class="media-item audio-card" data-lang="zh" data-title="show" data-variant="none">
                <div class="audio-thumb">
                    <div class="audio-art">🎵</div>
                    <span class="duration-pill">--:--</span>
                </div>
                <div class="image-meta">
                    <div class="media-title"><?= htmlspecialchars($title) ?></div>
                    <div class="media-info">示意音檔 · 中 · 鋒兄進化Show🔥 · 原版</div>
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
            <?php
            $filters = [
                ['label' => '全部', 'code' => 'all'],
                ['label' => '中', 'code' => 'zh'],
                ['label' => '英', 'code' => 'en'],
                ['label' => '日', 'code' => 'ja'],
                ['label' => '韓', 'code' => 'ko'],
                ['label' => '粵', 'code' => 'yue'],
            ];
            foreach ($filters as $f):
            ?>
            <button class="btn filter-lang" data-lang="<?= $f['code'] ?>" style="background:#f3f4f6; color:#111;"><?= $f['label'] ?></button>
            <?php endforeach; ?>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <?php
            $titles = [
                ['label'=>'全部主題','code'=>'all'],
                ['label'=>'鋒兄進化Show🔥','code'=>'show'],
                ['label'=>'鋒兄的傳奇人生','code'=>'legend'],
                ['label'=>'最瞎結婚理由','code'=>'marriage'],
                ['label'=>'塗哥水電王子爆紅','code'=>'tuge'],
            ];
            foreach ($titles as $t):
            ?>
            <button class="btn filter-title" data-title="<?= $t['code'] ?>" style="background:#f3f4f6; color:#111;"><?= $t['label'] ?></button>
            <?php endforeach; ?>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <?php
            $variants = [
                ['label'=>'全部人物','code'=>'all'],
                ['label'=>'Pekora','code'=>'pekora'],
                ['label'=>'Donald Trump','code'=>'trump'],
                ['label'=>'SpongeBob SquarePants','code'=>'spongebob'],
                ['label'=>'Sidhu','code'=>'sidhu'],
                ['label'=>'Rose','code'=>'rose'],
                ['label'=>'Freddie Mercury','code'=>'freddie'],
                ['label'=>'Hatsune Miku','code'=>'miku'],
            ];
            foreach ($variants as $v):
            ?>
            <button class="btn filter-variant" data-variant="<?= $v['code'] ?>" style="background:#f3f4f6; color:#111;"><?= $v['label'] ?></button>
            <?php endforeach; ?>
        </div>
        <div class="card" id="lyricsPlayer">
            <h4 id="lyricsTitle" style="margin-bottom:6px;">選擇左側歌曲開始播放</h4>
            <audio id="lyricsAudio" controls style="width:100%;"></audio>
            <div id="panelLang" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
                <?php foreach (['zh'=>'中','en'=>'英','ja'=>'日','ko'=>'韓','yue'=>'粵'] as $code=>$label): ?>
                <button class="btn lyrics-pill lyrics-lang" data-lang="<?= $code ?>" style="background:#f3f4f6; color:#111;"><?= $label ?></button>
                <?php endforeach; ?>
            </div>
            <div id="panelVariant" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;">
                <?php foreach (['pekora'=>'Pekora','trump'=>'Donald Trump','spongebob'=>'SpongeBob SquarePants','sidhu'=>'Sidhu','rose'=>'Rose','freddie'=>'Freddie Mercury','miku'=>'Hatsune Miku','none'=>'原版'] as $code=>$label): ?>
                <button class="btn lyrics-pill lyrics-variant" data-variant="<?= $code ?>" style="background:#f3f4f6; color:#111;"><?= $label ?></button>
                <?php endforeach; ?>
            </div>
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
