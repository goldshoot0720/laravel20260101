<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/SubscriptionOriginal.php';

function csvCandidates($basename) {
    $c = [
        __DIR__ . "/../{$basename}",
        __DIR__ . "/../uploads/{$basename}",
        __DIR__ . "/{$basename}",
    ];
    return array_values(array_filter($c, fn($p) => is_file($p)));
}

function parseISODateToYmd($iso) {
    if (!$iso) return null;
    try {
        $dt = new DateTime($iso);
        return $dt->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}

function parseUrlField($raw) {
    if (!$raw) return [null, null];
    // 可能以反引號包裹，包含逗號分段
    $raw = trim($raw, " \t\n\r\0\x0B`");
    $parts = array_map('trim', explode(',', $raw));
    $url = $parts[0] ?? null;
    $extra = $parts[1] ?? null;
    return [$url, $extra];
}

function importFoods($csvPath, PDO $db) {
    $f = fopen($csvPath, 'r');
    if (!$f) throw new Exception("無法開啟: {$csvPath}");
    // 使用反引號作為引號以處理含逗號欄位
    $header = fgetcsv($f, 0, ',', '`');
    $cols = array_map('trim', $header ?: []);
    $idx = array_flip($cols);
    $foodModel = new Food();
    $inserted = 0; $updated = 0; $skipped = 0;
    while (($row = fgetcsv($f, 0, ',', '`')) !== false) {
        if (count($row) === 0) continue;
        $data = array_map(fn($x) => is_string($x) ? trim($x) : $x, $row);
        $name = $data[$idx['name']] ?? null;
        if (!$name) { $skipped++; continue; }
        $amount = $data[$idx['amount']] ?? null;
        $price = $data[$idx['price']] ?? null;
        $shop = $data[$idx['shop']] ?? null;
        $todateIso = $data[$idx['todate']] ?? null;
        $photoRaw = $data[$idx['photo']] ?? null;
        [$photoUrl, $photoExtra] = parseUrlField($photoRaw);
        $todate = parseISODateToYmd($todateIso);
        $payload = [
            'name' => $name,
            'expiry_date' => $todate,
            'quantity' => is_numeric($amount) ? (int)$amount : null,
            'image_path' => $photoUrl,
            'price' => is_numeric($price) ? (int)$price : null,
            'location' => $shop,
        ];
        // upsert by name + todate
        $stmt = $db->prepare("SELECT id FROM food WHERE name = ? AND (todate <=> ?)");
        $stmt->execute([$name, $todate]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            $foodModel->updateFood($existing, $payload);
            $updated++;
        } else {
            $foodModel->createFood($payload);
            $inserted++;
        }
    }
    fclose($f);
    return ['inserted' => $inserted, 'updated' => $updated, 'skipped' => $skipped];
}

function importSubscriptions($csvPath, PDO $db) {
    $f = fopen($csvPath, 'r');
    if (!$f) throw new Exception("無法開啟: {$csvPath}");
    $header = fgetcsv($f, 0, ',', '`');
    $cols = array_map('trim', $header ?: []);
    $idx = array_flip($cols);
    $subModel = new SubscriptionOriginal();
    $inserted = 0; $updated = 0; $skipped = 0;
    while (($row = fgetcsv($f, 0, ',', '`')) !== false) {
        if (count($row) === 0) continue;
        $data = array_map(fn($x) => is_string($x) ? trim($x) : $x, $row);
        $name = $data[$idx['name']] ?? null;
        if (!$name) { $skipped++; continue; }
        $price = $data[$idx['price']] ?? null;
        $nextdateIso = $data[$idx['nextdate']] ?? null;
        $siteRaw = $data[$idx['site']] ?? null;
        $noteRaw = $data[$idx['note']] ?? null;
        [$siteUrl, $extra] = parseUrlField($siteRaw);
        $note = $noteRaw;
        if ($extra && mb_strpos($extra, '已取消') !== false) {
            $note = trim(($note ? $note . '；' : '') . $extra);
        }
        $nextdate = parseISODateToYmd($nextdateIso);
        $payload = [
            'name' => $name,
            'website_url' => $siteUrl,
            'price' => is_numeric($price) ? (int)$price : null,
            'next_payment_date' => $nextdate,
            'notes' => $note,
            'account_email' => null,
        ];
        // upsert by name + site
        $stmt = $db->prepare("SELECT id FROM subscription WHERE name = ? AND (site <=> ?)");
        $stmt->execute([$name, $siteUrl]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            $subModel->updateSubscription($existing, $payload);
            $updated++;
        } else {
            $subModel->createSubscription($payload);
            $inserted++;
        }
    }
    fclose($f);
    return ['inserted' => $inserted, 'updated' => $updated, 'skipped' => $skipped];
}

try {
    $db = getDB();
    $db->beginTransaction();
    $foodCsvs = csvCandidates('back4appfood.csv');
    $subCsvs = csvCandidates('back4appsubscription.csv');
    $foodRes = ['inserted'=>0,'updated'=>0,'skipped'=>0];
    $subRes = ['inserted'=>0,'updated'=>0,'skipped'=>0];
    if (!empty($foodCsvs)) {
        $foodRes = importFoods($foodCsvs[0], $db);
    }
    if (!empty($subCsvs)) {
        $subRes = importSubscriptions($subCsvs[0], $db);
    }
    $db->commit();
    header('Content-Type: text/plain; charset=utf-8');
    echo "匯入完成\n";
    echo "食品管理: 新增 {$foodRes['inserted']}，更新 {$foodRes['updated']}，略過 {$foodRes['skipped']}\n";
    echo "訂閱管理: 新增 {$subRes['inserted']}，更新 {$subRes['updated']}，略過 {$subRes['skipped']}\n";
    if (empty($foodCsvs)) echo "注意：未找到 back4appfood.csv\n";
    if (empty($subCsvs)) echo "注意：未找到 back4appsubscription.csv\n";
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "匯入失敗: " . $e->getMessage();
}
