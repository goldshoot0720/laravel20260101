<?php
$current_page = 'gallery';
$title = '鋒兄圖片庫 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🖼️</span>
        鋒兄圖片庫
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">智能的精美圖片AI創作 (241 張圖片)</small>
    </h1>
    <a href="#" class="btn btn-primary">新增圖片</a>
</div>

<!-- 搜尋和篩選 -->
<div class="card fade-in">
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <div class="search-box" style="flex: 1; min-width: 300px; margin-bottom: 0;">
            <input type="text" class="search-input" placeholder="搜尋圖片名稱或標籤...">
            <button class="search-btn">🔍</button>
        </div>
        <select style="padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
            <option>所有類型</option>
            <option>AI生成</option>
            <option>攝影作品</option>
            <option>插畫設計</option>
        </select>
        <select style="padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
            <option>按名稱排序</option>
            <option>按日期排序</option>
            <option>按大小排序</option>
        </select>
    </div>
</div>

<!-- 圖片統計 -->
<div class="card fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; gap: 30px;">
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #10b981;">625.95 MB</div>
                <div style="font-size: 12px; opacity: 0.7;">總大小</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #6366f1;">PNG: 192</div>
                <div style="font-size: 12px; opacity: 0.7;">格式分佈</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #8b5cf6;">JPG: 41</div>
                <div style="font-size: 12px; opacity: 0.7;">JPEG: 8</div>
            </div>
        </div>
        <div style="font-size: 14px; opacity: 0.7;">
            顯示 241 / 241 張圖片
        </div>
    </div>
</div>

<!-- 圖片網格 -->
<div class="card fade-in">
    <div class="media-grid">
        <!-- 示例圖片項目 -->
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=1" alt="AI生成圖片">
            <div class="media-overlay">
                <div class="media-title">1761405813-e...</div>
                <div class="media-info">JPG • 887 KB</div>
            </div>
        </div>
        
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=2" alt="AI生成圖片">
            <div class="media-overlay">
                <div class="media-title">1761405863-3...</div>
                <div class="media-info">JPG • 731 KB</div>
            </div>
        </div>
        
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=3" alt="AI生成圖片">
            <div class="media-overlay">
                <div class="media-title">1761405934-7...</div>
                <div class="media-info">JPG • 544 KB</div>
            </div>
        </div>
        
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=4" alt="貓咪照片">
            <div class="media-overlay">
                <div class="media-title">20240917_183...</div>
                <div class="media-info">PNG • 7.46 MB</div>
            </div>
        </div>
        
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=5" alt="節日圖片">
            <div class="media-overlay">
                <div class="media-title">202509_A4_2...</div>
                <div class="media-info">PNG • 9.78 MB</div>
            </div>
        </div>
        
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=6" alt="動漫角色">
            <div class="media-overlay">
                <div class="media-title">20251026_214...</div>
                <div class="media-info">JPG • 1.694 KB</div>
            </div>
        </div>
        
        <!-- 更多圖片項目 -->
        <?php for($i = 7; $i <= 18; $i++): ?>
        <div class="media-item">
            <img src="https://picsum.photos/300/300?random=<?= $i ?>" alt="圖片 <?= $i ?>">
            <div class="media-overlay">
                <div class="media-title">image_<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?>...</div>
                <div class="media-info">
                    <?= rand(0, 1) ? 'PNG' : 'JPG' ?> • 
                    <?= rand(100, 9999) ?> KB
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
    
    <!-- 載入更多 -->
    <div style="text-align: center; margin-top: 30px;">
        <button class="btn btn-primary">載入更多圖片</button>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>