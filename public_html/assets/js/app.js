// 鋒兄AI資訊系統 - 主要JavaScript文件

document.addEventListener('DOMContentLoaded', function() {
    // 初始化系統
    initializeSystem();
    
    // 綁定事件監聽器
    bindEventListeners();
    
    // 載入動畫
    animateElements();
});

// 系統初始化
function initializeSystem() {
    console.log('🔥 鋒兄AI資訊系統已啟動');
    
    // 檢查本地存儲
    if (!localStorage.getItem('feng_system_init')) {
        localStorage.setItem('feng_system_init', new Date().toISOString());
        showWelcomeMessage();
    }
    
    // 更新統計數據
    updateStatistics();
}

// 綁定事件監聽器
function bindEventListeners() {
    // 搜尋功能
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', handleSearch);
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    });
    
    // 搜尋按鈕
    const searchBtns = document.querySelectorAll('.search-btn');
    searchBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.search-input');
            performSearch(input.value);
        });
    });
    
    // 媒體項目點擊
    const mediaItems = document.querySelectorAll('.media-item');
    mediaItems.forEach(item => {
        item.addEventListener('click', function() {
            showMediaPreview(this);
        });
    });
    
    // 按鈕點擊效果
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            createRippleEffect(e, this);
        });
    });
    
    // 側邊欄響應式
    handleResponsiveSidebar();
    
    const filterPills = document.querySelectorAll('.filter-pill');
    filterPills.forEach(p => {
        p.addEventListener('click', () => {
            filterPills.forEach(x => x.classList.remove('active'));
            p.classList.add('active');
            const type = p.getAttribute('data-type');
            applyGalleryFilter(type);
        });
    });
    
    if (getCurrentPage() === 'gallery') {
        initGalleryLoader();
    }
    if (getCurrentPage() === 'videos') {
        initVideoDurations();
        const playButtons = document.querySelectorAll('.video-thumb .play-btn');
        playButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const card = btn.closest('.media-item');
                if (card) showMediaPreview(card);
            });
        });
    }
    if (getCurrentPage() === 'lyrics') {
        initAudioDurations();
        initLyricsSimplePlayer();
        initLyricsLanguageFilters();
        initLyricsTitleFilters();
        initLyricsVariantFilters();
    }
}

// 搜尋處理
function handleSearch(e) {
    const query = e.target.value.toLowerCase();
    const currentPage = getCurrentPage();
    
    if (query.length > 2) {
        debounce(() => performSearch(query), 300)();
    }
}

// 執行搜尋
function performSearch(query) {
    console.log('🔍 搜尋:', query);
    
    const currentPage = getCurrentPage();
    
    switch(currentPage) {
        case 'gallery':
            searchGallery(query);
            break;
        case 'food':
            searchFood(query);
            break;
        case 'subscription':
            searchSubscriptions(query);
            break;
    }
}

// 食品搜尋
function searchFood(query) {
    const foodCards = document.querySelectorAll('.card h3, .card h4');
    let visibleCount = 0;
    
    foodCards.forEach(title => {
        const card = title.closest('.card');
        const titleText = title.textContent.toLowerCase();
        
        if (titleText.includes(query) || query === '') {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateSearchResults(visibleCount, foodCards.length);
}

// 訂閱搜尋
function searchSubscriptions(query) {
    const subCards = document.querySelectorAll('.card h3, .card h4');
    let visibleCount = 0;
    
    subCards.forEach(title => {
        const card = title.closest('.card');
        const titleText = title.textContent.toLowerCase();
        
        if (titleText.includes(query) || query === '') {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateSearchResults(visibleCount, subCards.length);
}

// 更新搜尋結果
function updateSearchResults(visible, total) {
    console.log(`📊 顯示 ${visible} / ${total} 項目`);
}

// 圖片搜尋
function searchGallery(query) {
    const items = document.querySelectorAll('.media-item');
    let visibleCount = 0;
    items.forEach(item => {
        const titleEl = item.querySelector('.media-title');
        const titleText = (titleEl?.textContent || '').toLowerCase();
        if (titleText.includes(query) || query === '') {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    updateSearchResults(visibleCount, items.length);
}

function applyGalleryFilter(type) {
    const items = document.querySelectorAll('.media-item');
    let visibleCount = 0;
    items.forEach(item => {
        const ext = item.getAttribute('data-ext') || '';
        const match = type === 'all' || (type === 'jpg' ? ['jpg','jpeg'].includes(ext) : type === ext);
        item.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });
    updateSearchResults(visibleCount, items.length);
}

function initGalleryLoader() {
    const loader = document.getElementById('galleryLoader');
    if (!loader) return;
    loader.style.display = 'flex';
    const images = document.querySelectorAll('.image-thumb img');
    let loaded = 0;
    const done = () => {
        loader.style.display = 'none';
    };
    if (images.length === 0) {
        // 無圖片，直接隱藏
        done();
        return;
    }
    images.forEach(img => {
        if (img.complete) {
            loaded++;
            if (loaded >= images.length) done();
        } else {
            img.addEventListener('load', () => {
                loaded++;
                if (loaded >= images.length) done();
            });
            img.addEventListener('error', () => {
                loaded++;
                if (loaded >= images.length) done();
            });
        }
    });
}

function initVideoDurations() {
    const cards = document.querySelectorAll('.video-thumb');
    cards.forEach(card => {
        const video = card.querySelector('video');
        const pill = card.querySelector('.video-duration');
        if (!video || !pill) return;
        const setText = (sec) => {
            if (!isFinite(sec) || sec <= 0) {
                pill.textContent = '--:--';
                return;
            }
            const h = Math.floor(sec / 3600);
            const m = Math.floor((sec % 3600) / 60);
            const s = Math.floor(sec % 60);
            const mm = (h > 0 ? String(m).padStart(2,'0') : String(m));
            const ss = String(s).padStart(2,'0');
            pill.textContent = h > 0 ? `${h}:${mm}:${ss}` : `${mm}:${ss}`;
        };
        if (video.readyState >= 1) {
            setText(video.duration);
        } else {
            video.addEventListener('loadedmetadata', () => setText(video.duration));
            video.addEventListener('error', () => setText(NaN));
        }
    });
}

function initAudioDurations() {
    const cards = document.querySelectorAll('.audio-thumb');
    cards.forEach(card => {
        const audio = card.querySelector('audio');
        const pill = card.querySelector('.audio-duration');
        if (!pill) return;
        const setText = (sec) => {
            if (!isFinite(sec) || sec <= 0) {
                pill.textContent = '--:--';
                return;
            }
            const h = Math.floor(sec / 3600);
            const m = Math.floor((sec % 3600) / 60);
            const s = Math.floor(sec % 60);
            const mm = (h > 0 ? String(m).padStart(2,'0') : String(m));
            const ss = String(s).padStart(2,'0');
            pill.textContent = h > 0 ? `${h}:${mm}:${ss}` : `${mm}:${ss}`;
        };
        if (audio) {
            if (audio.readyState >= 1) {
                setText(audio.duration);
            } else {
                audio.addEventListener('loadedmetadata', () => setText(audio.duration));
                audio.addEventListener('error', () => setText(NaN));
            }
        } else {
            setText(NaN);
        }
    });
}

function initLyricsSimplePlayer() {
    const audioCards = document.querySelectorAll('.audio-card');
    const player = document.getElementById('lyricsAudio');
    const titleEl = document.getElementById('lyricsTitle');
    const textEl = document.getElementById('lyricsText');
    const progress = document.getElementById('lyricsProgress');
    const fill = document.getElementById('lyricsProgressFill');
    const curEl = document.getElementById('lyricsCurrent');
    const durEl = document.getElementById('lyricsDuration');
    const panelLang = document.querySelectorAll('.lyrics-lang');
    const panelVariant = document.querySelectorAll('.lyrics-variant');
    const variantRow = document.getElementById('panelVariant');
    const meta = [];
    let current = { title: 'other', lang: 'zh', variant: 'none' };
    audioCards.forEach(card => {
        const audio = card.querySelector('audio');
        meta.push({
            el: card,
            src: audio?.getAttribute('src') || '',
            name: card.querySelector('.media-title')?.textContent || '',
            lang: card.getAttribute('data-lang') || 'zh',
            title: card.getAttribute('data-title') || 'other',
            variant: card.getAttribute('data-variant') || 'none'
        });
    });
    
    if (player) {
        const fmt = (sec) => {
            if (!isFinite(sec) || sec <= 0) return '0:00';
            const h = Math.floor(sec / 3600);
            const m = Math.floor((sec % 3600) / 60);
            const s = Math.floor(sec % 60);
            const mm = h > 0 ? String(m).padStart(2,'0') : String(m);
            const ss = String(s).padStart(2,'0');
            return h > 0 ? `${h}:${mm}:${ss}` : `${mm}:${ss}`;
        };
        player.addEventListener('loadedmetadata', () => {
            if (durEl) durEl.textContent = fmt(player.duration);
        });
        player.addEventListener('timeupdate', () => {
            if (curEl) curEl.textContent = fmt(player.currentTime);
            if (fill) {
                const pct = player.duration ? (player.currentTime / player.duration) * 100 : 0;
                fill.style.width = `${pct}%`;
            }
        });
        if (progress) {
            progress.addEventListener('click', (e) => {
                const rect = progress.getBoundingClientRect();
                const ratio = (e.clientX - rect.left) / rect.width;
                if (player.duration) player.currentTime = Math.max(0, Math.min(player.duration * ratio, player.duration));
            });
        }
    }
    
    function applyPanelUI() {
        panelLang.forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-lang') === current.lang);
        });
        panelVariant.forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-variant') === current.variant);
        });
        if (variantRow) variantRow.style.display = current.lang === 'zh' ? 'flex' : 'none';
    }
    function playFromMeta(m) {
        if (!m || !player) return;
        player.src = m.src;
        player.play().catch(() => {});
        if (titleEl) titleEl.textContent = m.name;
        if (textEl) {
            const txt = m.src.replace(/\.(mp3|wav|m4a)$/i, '.txt');
            fetch(txt).then(r => r.ok ? r.text() : Promise.reject('no txt'))
            .then(text => { textEl.textContent = text; })
            .catch(() => {});
        }
    }
    function selectByCurrent() {
        const found = meta.find(x => x.title === current.title && x.lang === current.lang && (current.lang !== 'zh' || x.variant === current.variant));
        playFromMeta(found);
        applyPanelUI();
    }
    
    audioCards.forEach(card => {
        card.addEventListener('click', () => {
            const audio = card.querySelector('audio');
            const src = audio?.getAttribute('src') || '';
            const name = card.querySelector('.media-title')?.textContent || '未知歌曲';
            if (!player || !src) return;
            current.title = card.getAttribute('data-title') || 'other';
            current.lang = card.getAttribute('data-lang') || 'zh';
            current.variant = card.getAttribute('data-variant') || 'none';
            selectByCurrent();
        });
    });
    panelLang.forEach(b => {
        b.addEventListener('click', () => {
            current.lang = b.getAttribute('data-lang') || 'zh';
            selectByCurrent();
        });
    });
    panelVariant.forEach(b => {
        b.addEventListener('click', () => {
            current.variant = b.getAttribute('data-variant') || 'none';
            selectByCurrent();
        });
    });
}

function initLyricsLanguageFilters() {
    const pills = document.querySelectorAll('.filter-lang');
    pills.forEach(p => {
        p.addEventListener('click', () => {
            const val = p.getAttribute('data-lang') || 'all';
            pills.forEach(x => x.classList.remove('active'));
            p.classList.add('active');
            applyLyricsFilters({ lang: val });
        });
    });
}

function initLyricsTitleFilters() {
    const pills = document.querySelectorAll('.filter-title');
    pills.forEach(p => {
        p.addEventListener('click', () => {
            const val = p.getAttribute('data-title') || 'all';
            pills.forEach(x => x.classList.remove('active'));
            p.classList.add('active');
            applyLyricsFilters({ title: val });
        });
    });
}

function initLyricsVariantFilters() {
    const pills = document.querySelectorAll('.filter-variant');
    pills.forEach(p => {
        p.addEventListener('click', () => {
            const val = p.getAttribute('data-variant') || 'all';
            pills.forEach(x => x.classList.remove('active'));
            p.classList.add('active');
            applyLyricsFilters({ variant: val });
        });
    });
}

const lyricsFilterState = { lang: 'all', title: 'all', variant: 'all' };
function applyLyricsFilters(partial) {
    Object.assign(lyricsFilterState, partial);
    const cards = document.querySelectorAll('.audio-card');
    cards.forEach(card => {
        const lang = card.getAttribute('data-lang') || 'zh';
        const title = card.getAttribute('data-title') || 'other';
        const variant = card.getAttribute('data-variant') || 'none';
        const okLang = lyricsFilterState.lang === 'all' || lyricsFilterState.lang === lang;
        const okTitle = lyricsFilterState.title === 'all' || lyricsFilterState.title === title;
        const okVariant = lyricsFilterState.variant === 'all' || lyricsFilterState.variant === variant;
        card.style.display = (okLang && okTitle && okVariant) ? '' : 'none';
    });
}

// 已移除 LRC 解析，改為純文字顯示

// 媒體預覽
function showMediaPreview(item) {
    const title = item.querySelector('.media-title')?.textContent || '未知項目';
    const info = item.querySelector('.media-info')?.textContent || '';
    const videoEl = item.querySelector('.video-thumb video');
    const audioEl = item.querySelector('.audio-thumb audio');
    const imgEl = item.querySelector('.video-thumb img') || item.querySelector('img');
    const src = videoEl?.getAttribute('src') || audioEl?.getAttribute('src') || imgEl?.getAttribute('src') || '';
    const isVideo = !!videoEl && !!src;
    const isAudio = !!audioEl && !!src;
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    const bodyContent = isVideo
        ? `<video src="${src}" controls autoplay style="width:100%; height:auto;" playsinline></video>`
        : (isAudio
            ? `<audio src="${src}" controls autoplay style="width:100%;"></audio>`
            : `<img src="${src}" alt="${title}" style="max-width:100%; border-radius:8px;">`);
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${bodyContent}
                <div style="margin-top:12px; opacity:.85;">${info}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // 綁定關閉事件
    modal.querySelector('.modal-close').addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// 按鈕漣漪效果
function createRippleEffect(event, button) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    `;
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => {
        if (ripple.parentNode) {
            ripple.parentNode.removeChild(ripple);
        }
    }, 600);
}

// 響應式側邊欄
function handleResponsiveSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 768) {
        // 移動端：添加漢堡菜單
        if (!document.querySelector('.mobile-menu-btn')) {
            const menuBtn = document.createElement('button');
            menuBtn.className = 'mobile-menu-btn';
            menuBtn.innerHTML = '☰';
            menuBtn.style.cssText = `
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: rgba(0,0,0,0.7);
                color: white;
                border: none;
                padding: 10px;
                border-radius: 8px;
                font-size: 18px;
                cursor: pointer;
            `;
            
            document.body.appendChild(menuBtn);
            
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
    }
}

// 載入動畫
function animateElements() {
    const elements = document.querySelectorAll('.fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
}

// 更新統計數據
function updateStatistics() {
    // 模擬數據更新
    const stats = {
        foods: 15,
        subscriptions: 24
    };
    
    // 更新儀表板統計
    updateDashboardStats(stats);
}

// 更新儀表板統計
function updateDashboardStats(stats) {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach((el, index) => {
        const values = [stats.foods, stats.subscriptions];
        if (values[index]) {
            animateNumber(el, 0, values[index], 1000);
        }
    });
}

// 數字動畫
function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * progress);
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

// 歡迎訊息
function showWelcomeMessage() {
    console.log('🎉 歡迎使用鋒兄AI資訊系統！');
}

// 獲取當前頁面
function getCurrentPage() {
    const path = window.location.pathname;
    if (path === '/' || path.includes('gallery')) return 'gallery';
    if (path.includes('food')) return 'food';
    if (path.includes('subscription')) return 'subscription';
    if (path.includes('videos')) return 'videos';
    if (path.includes('lyrics')) return 'lyrics';
    if (path.includes('bank')) return 'bank';
    if (path.includes('about')) return 'about';
    return 'dashboard';
}

// 防抖函數
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 添加CSS動畫
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease;
    }
    
    .modal-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        color: white;
        animation: slideIn 0.3s ease;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
`;

document.head.appendChild(style);
