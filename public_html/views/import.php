<?php
$current_page = 'import';
$title = 'CSV匯入 - ' . SYSTEM_NAME;

ob_start();
?>

<div class="header">
    <h1 class="page-title">
        <span>📥</span>
        CSV匯入
        <small style="font-size: 14px; opacity: 0.7; margin-left: 10px;">從檔案總管選擇 CSV 檔上傳並匯入</small>
    </h1>
    <a href="/import" class="btn btn-primary">重新載入</a>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h3 class="card-title">選擇檔案</h3>
        <span style="font-size: 12px; opacity: 0.7;">支援 back4appfood.csv 與 back4appsubscription.csv</span>
    </div>
    <form action="/tools/upload_csv.php" method="post" enctype="multipart/form-data" style="display:grid; gap:12px;">
        <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">食品管理 CSV（back4appfood.csv）</label>
            <input type="file" name="food_csv" accept=".csv,text/csv,application/vnd.ms-excel">
        </div>
        <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">訂閱管理 CSV（back4appsubscription.csv）</label>
            <input type="file" name="sub_csv" accept=".csv,text/csv,application/vnd.ms-excel">
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-success" type="submit">上傳並匯入</button>
            <a class="btn btn-primary" href="/tools/import_back4app.php">只執行匯入（已上傳）</a>
        </div>
        <div style="font-size:12px; opacity:.7;">
            上傳後會將檔案儲存為 back4appfood.csv 與 back4appsubscription.csv，並自動跳轉至匯入頁顯示結果。
        </div>
    </form>

<?php
$content = ob_get_clean();
include 'layout.php';
