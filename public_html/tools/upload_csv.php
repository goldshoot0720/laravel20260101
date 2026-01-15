<?php
// 接受兩個CSV上傳並保存為標準檔名，完成後導向匯入腳本
$uploadDirRoot = __DIR__ . '/../';
$allowedNames = [
    'food_csv' => 'back4appfood.csv',
    'sub_csv'  => 'back4appsubscription.csv'
];

function isCsvFile($name, $tmp) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($ext !== 'csv') return false;
    // 基本檢查大小
    if (!is_uploaded_file($tmp)) return false;
    return true;
}

$saved = [];
foreach ($allowedNames as $field => $targetName) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        continue;
    }
    $name = $_FILES[$field]['name'];
    $tmp  = $_FILES[$field]['tmp_name'];
    if (!isCsvFile($name, $tmp)) {
        continue;
    }
    // 儲存到根目錄（tools/import_back4app.php 支援從此處尋找）
    $targetPath = $uploadDirRoot . $targetName;
    // 覆蓋舊檔
    if (file_exists($targetPath)) @unlink($targetPath);
    if (!move_uploaded_file($tmp, $targetPath)) {
        continue;
    }
    $saved[] = $targetName;
}

if (count($saved) === 0) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(400);
    echo "未接收到有效的CSV檔案";
    exit;
}

// 上傳成功，導向匯入
header('Location: /tools/import_back4app.php');
exit;
