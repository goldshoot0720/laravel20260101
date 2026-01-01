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

// 載入資料庫配置和模型
require_once '../config/database.php';
require_once '../models/Gallery.php';
require_once '../models/Video.php';
require_once '../models/Food.php';
require_once '../models/SubscriptionOriginal.php';

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
    
    try {
        $gallery = new Gallery();
        $video = new Video();
        $food = new Food();
        $subscription = new SubscriptionOriginal();
        
        $galleryStats = $gallery->getStatistics();
        $videoStats = $video->getStatistics();
        $foodStats = $food->getStatistics();
        $subscriptionStats = $subscription->getStatistics();
        
        $stats = [
            'images' => [
                'total' => $galleryStats['total'],
                'size' => $galleryStats['total_size_formatted'],
                'ai_generated' => $galleryStats['ai_generated'],
                'formats' => []
            ],
            'videos' => [
                'total' => $videoStats['total'],
                'size' => $videoStats['total_size_formatted'],
                'duration' => $videoStats['total_duration_formatted']
            ],
            'food' => [
                'total' => $foodStats['total'],
                'expiring_3_days' => $foodStats['expiring_3_days'],
                'expiring_7_days' => $foodStats['expiring_7_days'],
                'expiring_30_days' => $foodStats['expiring_30_days'],
                'expired' => $foodStats['expired']
            ],
            'subscription' => [
                'total' => $subscriptionStats['total'],
                'active' => $subscriptionStats['active'],
                'expiring_3_days' => $subscriptionStats['expiring_3_days'],
                'expiring_7_days' => $subscriptionStats['expiring_7_days'],
                'expired' => $subscriptionStats['expired'],
                'monthly_cost' => $subscriptionStats['monthly_cost'],
                'yearly_cost' => $subscriptionStats['yearly_cost']
            ]
        ];
        
        // 處理圖片格式統計
        foreach ($galleryStats['type_stats'] as $type) {
            $stats['images']['formats'][$type['file_type']] = $type['count'];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        
    } catch (Exception $e) {
        throw new Exception('獲取統計數據失敗: ' . $e->getMessage(), 500);
    }
}

// 圖片庫處理
function handleGallery($method, $input) {
    $gallery = new Gallery();
    
    switch ($method) {
        case 'GET':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 20;
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            
            try {
                if ($search) {
                    $images = $gallery->searchImages($search, $limit);
                    $total = count($images);
                } elseif ($category) {
                    $images = $gallery->getByCategory($category, $limit);
                    $total = $gallery->count(['category' => $category]);
                } else {
                    $result = $gallery->paginate($page, $limit);
                    $images = $result['data'];
                    $total = $result['total'];
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $images,
                    'pagination' => [
                        'page' => (int)$page,
                        'limit' => (int)$limit,
                        'total' => $total
                    ]
                ]);
                
            } catch (Exception $e) {
                throw new Exception('獲取圖片數據失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'POST':
            try {
                $image = $gallery->createImage($input);
                
                echo json_encode([
                    'success' => true,
                    'message' => '圖片新增成功',
                    'data' => $image
                ]);
                
            } catch (Exception $e) {
                throw new Exception('新增圖片失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'PUT':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('缺少圖片ID', 400);
            }
            
            try {
                $image = $gallery->updateImage($id, $input);
                
                echo json_encode([
                    'success' => true,
                    'message' => '圖片更新成功',
                    'data' => $image
                ]);
                
            } catch (Exception $e) {
                throw new Exception('更新圖片失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('缺少圖片ID', 400);
            }
            
            try {
                $gallery->delete($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => '圖片刪除成功'
                ]);
                
            } catch (Exception $e) {
                throw new Exception('刪除圖片失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 影片庫處理
function handleVideos($method, $input) {
    $video = new Video();
    
    switch ($method) {
        case 'GET':
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            
            try {
                if ($search) {
                    $videos = $video->searchVideos($search);
                } elseif ($category) {
                    $videos = $video->getByCategory($category);
                } else {
                    $videos = $video->getAllVideos();
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $videos
                ]);
                
            } catch (Exception $e) {
                throw new Exception('獲取影片數據失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'POST':
            try {
                $videoRecord = $video->createVideo($input);
                
                echo json_encode([
                    'success' => true,
                    'message' => '影片新增成功',
                    'data' => $videoRecord
                ]);
                
            } catch (Exception $e) {
                throw new Exception('新增影片失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 食品管理處理
function handleFood($method, $input) {
    $food = new Food();
    
    switch ($method) {
        case 'GET':
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $status = $_GET['status'] ?? '';
            
            try {
                if ($search) {
                    $foods = $food->searchFoods($search);
                } elseif ($category) {
                    $foods = $food->getByCategory($category);
                } elseif ($status) {
                    $foods = $food->getByStatus($status);
                } else {
                    $foods = $food->getAllFoods();
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $foods
                ]);
                
            } catch (Exception $e) {
                throw new Exception('獲取食品數據失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'POST':
            try {
                $foodItem = $food->createFood($input);
                
                echo json_encode([
                    'success' => true,
                    'message' => '食品新增成功',
                    'data' => $foodItem
                ]);
                
            } catch (Exception $e) {
                throw new Exception('新增食品失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        default:
            throw new Exception('方法不允許', 405);
    }
}

// 訂閱管理處理
function handleSubscription($method, $input) {
    $subscription = new SubscriptionOriginal();
    
    switch ($method) {
        case 'GET':
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $status = $_GET['status'] ?? '';
            
            try {
                if ($search) {
                    $subscriptions = $subscription->searchSubscriptions($search);
                } elseif ($category) {
                    $subscriptions = $subscription->getByCategory($category);
                } elseif ($status) {
                    $subscriptions = $subscription->getByStatus($status);
                } else {
                    $subscriptions = $subscription->getAllSubscriptions();
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $subscriptions
                ]);
                
            } catch (Exception $e) {
                throw new Exception('獲取訂閱數據失敗: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'POST':
            try {
                $sub = $subscription->createSubscription($input);
                
                echo json_encode([
                    'success' => true,
                    'message' => '訂閱新增成功',
                    'data' => $sub
                ]);
                
            } catch (Exception $e) {
                throw new Exception('新增訂閱失敗: ' . $e->getMessage(), 500);
            }
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
    
    if (empty($query)) {
        throw new Exception('搜尋關鍵字不能為空', 400);
    }
    
    try {
        $results = [
            'images' => [],
            'videos' => [],
            'food' => [],
            'subscriptions' => []
        ];
        
        if ($type === 'all' || $type === 'images') {
            $gallery = new Gallery();
            $results['images'] = $gallery->searchImages($query, 10);
        }
        
        if ($type === 'all' || $type === 'videos') {
            $video = new Video();
            $results['videos'] = $video->searchVideos($query, 10);
        }
        
        if ($type === 'all' || $type === 'food') {
            $food = new Food();
            $results['food'] = $food->searchFoods($query, 10);
        }
        
        if ($type === 'all' || $type === 'subscriptions') {
            $subscription = new SubscriptionOriginal();
            $results['subscriptions'] = $subscription->searchSubscriptions($query, 10);
        }
        
        echo json_encode([
            'success' => true,
            'query' => $query,
            'type' => $type,
            'results' => $results
        ]);
        
    } catch (Exception $e) {
        throw new Exception('搜尋失敗: ' . $e->getMessage(), 500);
    }
}
?>