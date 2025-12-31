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
            searchImages(query);
            break;
        case 'videos':
            searchVideos(query);
            break;
        case 'food':
            searchFood(query);
            break;
        case 'subscription':
            searchSubscriptions(query);
            break;
    }
}

// 圖片搜尋
function searchImages(query) {
    const mediaItems = document.querySelectorAll('.media-item');
    let visibleCount = 0;
    
    mediaItems.forEach(item => {
        const title = item.querySelector('.media-title')?.textContent.toLowerCase() || '';
        const info = item.querySelector('.media-info')?.textContent.toLowerCase() || '';
        
        if (title.includes(query) || info.includes(query) || query === '') {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    updateSearchResults(visibleCount, mediaItems.length);
}

// 影片搜尋
function searchVideos(query) {
    const videoCards = document.querySelectorAll('.card h3');
    let visibleCount = 0;
    
    videoCards.forEach(title => {
        const card = title.closest('.card');
        const titleText = title.textContent.toLowerCase();
        
        if (titleText.includes(query) || query === '') {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateSearchResults(visibleCount, videoCards.length);
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

// 媒體預覽
function showMediaPreview(item) {
    const title = item.querySelector('.media-title')?.textContent || '未知項目';
    const info = item.querySelector('.media-info')?.textContent || '';
    
    // 創建預覽模態框
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>${info}</p>
                <p>點擊項目查看詳細資訊</p>
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
        images: 241,
        videos: 2,
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
        const values = [stats.images, stats.videos, stats.foods, stats.subscriptions];
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
    if (path.includes('gallery')) return 'gallery';
    if (path.includes('videos')) return 'videos';
    if (path.includes('food')) return 'food';
    if (path.includes('subscription')) return 'subscription';
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