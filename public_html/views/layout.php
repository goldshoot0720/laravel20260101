<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? SYSTEM_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>
    <!-- 側邊欄 -->
    <nav class="sidebar">
        <div class="logo">
            <div class="logo-icon">鋒</div>
            <div>
                <div style="font-weight: 600;">鋒兄塗哥公關資訊</div>
                <div style="font-size: 12px; opacity: 0.7;">歡迎使用鋒兄塗哥公關資訊</div>
            </div>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link <?= ($current_page ?? '') === 'gallery' ? 'active' : '' ?>">
                    <span class="nav-icon">🏠</span>
                    <span>首頁</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/dashboard" class="nav-link <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span>儀表板</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/food" class="nav-link <?= ($current_page ?? '') === 'food' ? 'active' : '' ?>">
                    <span class="nav-icon">🍎</span>
                    <span>食品管理</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/subscription" class="nav-link <?= ($current_page ?? '') === 'subscription' ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span>
                    <span>訂閱管理</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/videos" class="nav-link <?= ($current_page ?? '') === 'videos' ? 'active' : '' ?>">
                    <span class="nav-icon">🎬</span>
                    <span>影片介紹</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/lyrics" class="nav-link <?= ($current_page ?? '') === 'lyrics' ? 'active' : '' ?>">
                    <span class="nav-icon">🎵</span>
                    <span>鋒兄音樂歌詞</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/bank" class="nav-link <?= ($current_page ?? '') === 'bank' ? 'active' : '' ?>">
                    <span class="nav-icon">🏦</span>
                    <span>銀行統計</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/about" class="nav-link <?= ($current_page ?? '') === 'about' ? 'active' : '' ?>">
                    <span class="nav-icon">ℹ️</span>
                    <span>關於我們</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/import" class="nav-link <?= ($current_page ?? '') === 'import' ? 'active' : '' ?>">
                    <span class="nav-icon">📥</span>
                    <span>CSV匯入</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- 主內容區 -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <script src="/assets/js/app.js"></script>
</body>
</html>
