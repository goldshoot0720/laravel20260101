<?php
$current_page = 'food';
$title = '食品管理系統 - ' . SYSTEM_NAME;

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
            <input type="text" class="search-input" placeholder="搜尋食品名稱或編號...">
            <button class="search-btn">🔍</button>
        </div>
        <button class="btn btn-primary">重新載入</button>
    </div>
</div>

<!-- 食品項目 -->
<div class="grid grid-2">
    <!-- 食品項目 1 -->
    <div class="card fade-in">
        <div style="display: flex; gap: 15px;">
            <div style="width: 80px; height: 80px; background: linear-gradient(45deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                🍘
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <h3>【張君雅】五香海苔休閒丸子</h3>
                    <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">新鮮</span>
                </div>
                <div style="font-size: 14px; opacity: 0.7; margin-bottom: 8px;">
                    <div>數量: 3</div>
                    <div>價格: NT$ 0</div>
                    <div>地點: 未設定</div>
                </div>
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 15px;">
                    <div>到期日期: 2026-01-06</div>
                    <div>剩餘天數: 15 天</div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">編輯</button>
                    <button class="btn btn-error" style="font-size: 12px; padding: 6px 12px;">🗑️</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 食品項目 2 -->
    <div class="card fade-in">
        <div style="display: flex; gap: 15px;">
            <div style="width: 80px; height: 80px; background: linear-gradient(45deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                🍘
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <h3>【張君雅】日式串燒休閒丸子</h3>
                    <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">新鮮</span>
                </div>
                <div style="font-size: 14px; opacity: 0.7; margin-bottom: 8px;">
                    <div>數量: 6</div>
                    <div>價格: NT$ 0</div>
                    <div>地點: 未設定</div>
                </div>
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 15px;">
                    <div>到期日期: 2026-01-07</div>
                    <div>剩餘天數: 16 天</div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">編輯</button>
                    <button class="btn btn-error" style="font-size: 12px; padding: 6px 12px;">🗑️</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 更多食品項目 -->
<div class="grid grid-3">
    <?php 
    $foods = [
        ['name' => '有機蘋果', 'icon' => '🍎', 'qty' => 5, 'days' => 7, 'status' => 'warning'],
        ['name' => '新鮮牛奶', 'icon' => '🥛', 'qty' => 2, 'days' => 3, 'status' => 'error'],
        ['name' => '全麥麵包', 'icon' => '🍞', 'qty' => 1, 'days' => 5, 'status' => 'warning'],
        ['name' => '雞蛋', 'icon' => '🥚', 'qty' => 12, 'days' => 14, 'status' => 'success'],
        ['name' => '香蕉', 'icon' => '🍌', 'qty' => 8, 'days' => 4, 'status' => 'warning'],
        ['name' => '優格', 'icon' => '🍦', 'qty' => 3, 'days' => 10, 'status' => 'success'],
        ['name' => '胡蘿蔔', 'icon' => '🥕', 'qty' => 6, 'days' => 21, 'status' => 'success'],
        ['name' => '番茄', 'icon' => '🍅', 'qty' => 4, 'days' => 6, 'status' => 'warning'],
        ['name' => '起司', 'icon' => '🧀', 'qty' => 1, 'days' => 30, 'status' => 'success'],
        ['name' => '火腿', 'icon' => '🥓', 'qty' => 200, 'days' => 15, 'status' => 'success'],
        ['name' => '洋蔥', 'icon' => '🧅', 'qty' => 3, 'days' => 25, 'status' => 'success'],
    ];
    
    foreach($foods as $food):
        $statusColor = [
            'success' => '#10b981',
            'warning' => '#f59e0b', 
            'error' => '#ef4444'
        ][$food['status']];
        
        $statusText = [
            'success' => '新鮮',
            'warning' => '即將到期',
            'error' => '緊急'
        ][$food['status']];
    ?>
    <div class="card fade-in" style="padding: 15px;">
        <div style="text-align: center; margin-bottom: 10px;">
            <div style="font-size: 32px; margin-bottom: 8px;"><?= $food['icon'] ?></div>
            <h4 style="font-size: 14px; margin-bottom: 5px;"><?= $food['name'] ?></h4>
            <div style="background: <?= $statusColor ?>; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px; display: inline-block;">
                <?= $statusText ?>
            </div>
        </div>
        <div style="font-size: 12px; opacity: 0.7; text-align: center;">
            <div>數量: <?= $food['qty'] ?></div>
            <div>剩餘: <?= $food['days'] ?> 天</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>