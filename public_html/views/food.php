<?php
$current_page = 'food';
$title = '食品管理系統 - ' . SYSTEM_NAME;

// 載入食品數據
require_once 'models/Food.php';
$food = new Food();
$foods = $food->getAllFoods();

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>🍎</span>
        食品管理系統
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">智能的食品保存及訓練狀態</small>
    </h1>
    <div style="display: flex; gap: 10px;">
        <a href="#" class="btn btn-success">新增食品</a>
        <a href="#" class="btn btn-primary">重新載入</a>
    </div>
</div>

<!-- 搜尋和篩選 -->
<div class="card fade-in">
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <div class="search-box" style="flex: 1; min-width: 300px; margin-bottom: 0;">
            <input type="text" class="search-input" placeholder="搜尋食品名稱或商店...">
            <button class="search-btn">🔍</button>
        </div>
        <button class="btn btn-primary">重新載入</button>
    </div>
</div>

<!-- 食品項目 -->
<div class="grid grid-2">
    <?php foreach($foods as $item): 
        $daysLeft = $food->getDaysLeft($item['todate']);
        $status = $food->getStatus($item['todate']);
        $statusColor = [
            'success' => '#10b981',
            'warning' => '#f59e0b', 
            'error' => '#ef4444',
            'expired' => '#6b7280'
        ][$status];
        
        $statusText = [
            'success' => '新鮮',
            'warning' => '即將到期',
            'error' => '緊急',
            'expired' => '已過期'
        ][$status];
    ?>
    <div class="card fade-in">
        <div style="display: flex; gap: 15px;">
            <div style="width: 80px; height: 80px; background: linear-gradient(45deg, <?= $statusColor ?>, <?= $statusColor ?>aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                🍘
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <span style="background: <?= $statusColor ?>; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= $statusText ?>
                    </span>
                </div>
                <div style="font-size: 14px; opacity: 0.7; margin-bottom: 8px;">
                    <div>數量: <?= $item['amount'] ?></div>
                    <div>價格: <?= $food->formatPrice($item['price']) ?></div>
                    <div>地點: <?= htmlspecialchars($item['shop'] ?: '未設定') ?></div>
                </div>
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 15px;">
                    <div>到期日期: <?= $item['todate'] ?></div>
                    <div>剩餘天數: <?= $daysLeft >= 0 ? $daysLeft . ' 天' : '已過期 ' . abs($daysLeft) . ' 天' ?></div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">編輯</button>
                    <button class="btn btn-error" style="font-size: 12px; padding: 6px 12px;">🗑️</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- 如果沒有數據，顯示示例數據 -->
<?php if (empty($foods)): ?>
<div class="grid grid-3">
    <?php 
    $sampleFoods = [
        ['name' => '有機蘋果', 'icon' => '🍎', 'amount' => 5, 'days' => 7, 'status' => 'warning', 'price' => 150],
        ['name' => '新鮮牛奶', 'icon' => '🥛', 'amount' => 2, 'days' => 3, 'status' => 'error', 'price' => 65],
        ['name' => '全麥麵包', 'icon' => '🍞', 'amount' => 1, 'days' => 5, 'status' => 'warning', 'price' => 45],
        ['name' => '雞蛋', 'icon' => '🥚', 'amount' => 12, 'days' => 14, 'status' => 'success', 'price' => 80],
        ['name' => '香蕉', 'icon' => '🍌', 'amount' => 8, 'days' => 4, 'status' => 'warning', 'price' => 60],
        ['name' => '優格', 'icon' => '🍦', 'amount' => 3, 'days' => 10, 'status' => 'success', 'price' => 120],
        ['name' => '胡蘿蔔', 'icon' => '🥕', 'amount' => 6, 'days' => 21, 'status' => 'success', 'price' => 35],
        ['name' => '番茄', 'icon' => '🍅', 'amount' => 4, 'days' => 6, 'status' => 'warning', 'price' => 90],
        ['name' => '起司', 'icon' => '🧀', 'amount' => 1, 'days' => 30, 'status' => 'success', 'price' => 180],
        ['name' => '火腿', 'icon' => '🥓', 'amount' => 200, 'days' => 15, 'status' => 'success', 'price' => 250],
        ['name' => '洋蔥', 'icon' => '🧅', 'amount' => 3, 'days' => 25, 'status' => 'success', 'price' => 40],
    ];
    
    foreach($sampleFoods as $item):
        $statusColor = [
            'success' => '#10b981',
            'warning' => '#f59e0b', 
            'error' => '#ef4444'
        ][$item['status']];
        
        $statusText = [
            'success' => '新鮮',
            'warning' => '即將到期',
            'error' => '緊急'
        ][$item['status']];
    ?>
    <div class="card fade-in" style="padding: 15px;">
        <div style="text-align: center; margin-bottom: 10px;">
            <div style="font-size: 32px; margin-bottom: 8px;"><?= $item['icon'] ?></div>
            <h4 style="font-size: 14px; margin-bottom: 5px;"><?= $item['name'] ?></h4>
            <div style="background: <?= $statusColor ?>; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px; display: inline-block;">
                <?= $statusText ?>
            </div>
        </div>
        <div style="font-size: 12px; opacity: 0.7; text-align: center;">
            <div>數量: <?= $item['amount'] ?></div>
            <div>剩餘: <?= $item['days'] ?> 天</div>
            <div>價格: NT$ <?= $item['price'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>