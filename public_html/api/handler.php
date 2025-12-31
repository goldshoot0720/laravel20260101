<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 處理 OPTIONS 請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 獲取請求方法和路徑
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

// API 路由處理
try {
    switch ($path) {
        case 'stats':
            handleStats($method);
            break;
            
        case 'gallery':
            handleGallery($method, $input);
            break;
            
        case 'videos':
            handleVideos($method, $input);
            break;
            
        case 'food':
            handleFood($method, $input);
            break;
            
        case 'subscription':
            handleSubscription($method, $input);
            break;
            
        case 'search':
            handleSearch($method, $input);
            break;
            
        default:
            throw new Exception('API 端點不存在', 404);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}

// 統計數據處理
function handleStats($method) {
    if ($method !== 'GET') {
        throw new Exception('方法不允許', 405);
    }
    
    $stats = [
        'images' => [
            'total' => 241,
            'size' => '625.95 MB',
            'formats' => [
                'PNG' => 192,
                'JPG' => 41,
                'JPEG' => 8
            ]
        ],
        'videos' => [
            'total' => 2,
            'size' => '6.22 MB',
            'duration' => '02:08'
        ],
        'food' => [
            'total' => 15,
            'expiring_3_days' => 0,
            'expiring_7_days' => 0,
            'expiring_30_days' => 2
        ],
        'subscription' => [
            'total' => 24,
            'expiring_3_days' => 0,
            'expiring_7_days' => 1,
            'expired' => 0
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

// 圖片庫處理
function handleGallery($method, $input) {
    switch ($method) {
        case 'GET':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 20;
            $search = $_GET['search'] ?? '';
            
            // 模擬圖片數據
            $images = generateMockImages($page, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'data' => $images,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => 241
                ]
            ]);
            break;
            
        case 'POST':
            // 新增圖片
            echo json_encode([
                'success' => true,
                'message' => '圖片上傳成功',
                'data' => [
                    'id' => uniqid(),
                    'filename' => $input['filename'] ?? 'new_image.jpg',
                    'size' => $input['size'] ?? '1.2 MB',
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 影片庫處理
function handleVideos($method, $input) {
    switch ($method) {
        case 'GET':
            $videos = [
                [
                    'id' => 1,
                    'title' => '鋒兄的傳奇人生',
                    'description' => '鋒兄人生歷程紀錄片',
                    'duration' => '00:45',
                    'size' => '2.01 MB',
                    'format' => 'MP4',
                    'thumbnail' => '/assets/images/video1_thumb.jpg'
                ],
                [
                    'id' => 2,
                    'title' => '鋒兄進化Show 🔥',
                    'description' => '鋒兄進化歷程山歷程',
                    'duration' => '01:23',
                    'size' => '4.21 MB',
                    'format' => 'MP4',
                    'thumbnail' => '/assets/images/video2_thumb.jpg'
                ]
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $videos
            ]);
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 食品管理處理
function handleFood($method, $input) {
    switch ($method) {
        case 'GET':
            $foods = generateMockFoods();
            
            echo json_encode([
                'success' => true,
                'data' => $foods
            ]);
            break;
            
        case 'POST':
            // 新增食品
            echo json_encode([
                'success' => true,
                'message' => '食品新增成功',
                'data' => [
                    'id' => uniqid(),
                    'name' => $input['name'] ?? '新食品',
                    'quantity' => $input['quantity'] ?? 1,
                    'expiry_date' => $input['expiry_date'] ?? date('Y-m-d', strtotime('+30 days')),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 訂閱管理處理
function handleSubscription($method, $input) {
    switch ($method) {
        case 'GET':
            $subscriptions = generateMockSubscriptions();
            
            echo json_encode([
                'success' => true,
                'data' => $subscriptions
            ]);
            break;
            
        case 'POST':
            // 新增訂閱
            echo json_encode([
                'success' => true,
                'message' => '訂閱新增成功',
                'data' => [
                    'id' => uniqid(),
                    'name' => $input['name'] ?? '新訂閱',
                    'price' => $input['price'] ?? 0,
                    'next_payment' => $input['next_payment'] ?? date('Y-m-d', strtotime('+30 days')),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 搜尋處理
function handleSearch($method, $input) {
    if ($method !== 'POST') {
        throw new Exception('方法不允許', 405);
    }
    
    $query = $input['query'] ?? '';
    $type = $input['type'] ?? 'all';
    
    $results = [
        'images' => [],
        'videos' => [],
        'food' => [],
        'subscriptions' => []
    ];
    
    // 模擬搜尋結果
    if ($type === 'all' || $type === 'images') {
        $results['images'] = array_slice(generateMockImages(1, 10, $query), 0, 5);
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'results' => $results
    ]);
}

// 生成模擬圖片數據
function generateMockImages($page = 1, $limit = 20, $search = '') {
    $images = [];
    $start = ($page - 1) * $limit;
    
    for ($i = $start; $i < $start + $limit && $i < 241; $i++) {
        $images[] = [
            'id' => $i + 1,
            'filename' => 'image_' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '.jpg',
            'title' => '圖片 ' . ($i + 1),
            'size' => rand(100, 9999) . ' KB',
            'format' => rand(0, 1) ? 'PNG' : 'JPG',
            'url' => 'https://picsum.photos/300/300?random=' . ($i + 1),
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'))
        ];
    }
    
    return $images;
}

// 生成模擬食品數據
function generateMockFoods() {
    $foods = [
        ['name' => '【張君雅】五香海苔休閒丸子', 'quantity' => 3, 'days_left' => 15, 'status' => 'success'],
        ['name' => '【張君雅】日式串燒休閒丸子', 'quantity' => 6, 'days_left' => 16, 'status' => 'success'],
        ['name' => '有機蘋果', 'quantity' => 5, 'days_left' => 7, 'status' => 'warning'],
        ['name' => '新鮮牛奶', 'quantity' => 2, 'days_left' => 3, 'status' => 'error'],
        ['name' => '全麥麵包', 'quantity' => 1, 'days_left' => 5, 'status' => 'warning']
    ];
    
    return array_map(function($food, $index) {
        return array_merge($food, [
            'id' => $index + 1,
            'price' => 'NT$ ' . rand(50, 500),
            'location' => '未設定',
            'expiry_date' => date('Y-m-d', strtotime('+' . $food['days_left'] . ' days'))
        ]);
    }, $foods, array_keys($foods));
}

// 生成模擬訂閱數據
function generateMockSubscriptions() {
    $subscriptions = [
        ['name' => '天虎/黃信訊/心臟內科', 'price' => 530, 'days_left' => 1, 'status' => 'warning'],
        ['name' => 'kiro pro', 'price' => 640, 'days_left' => 10, 'status' => 'success'],
        ['name' => 'Netflix', 'price' => 390, 'days_left' => 15, 'status' => 'success'],
        ['name' => 'Spotify', 'price' => 149, 'days_left' => 8, 'status' => 'warning']
    ];
    
    return array_map(function($sub, $index) {
        return array_merge($sub, [
            'id' => $index + 1,
            'url' => 'https://example.com',
            'next_payment' => date('Y-m-d', strtotime('+' . $sub['days_left'] . ' days'))
        ]);
    }, $subscriptions, array_keys($subscriptions));
}
?>