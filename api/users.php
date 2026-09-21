<?php
header('Content-Type: application/json');
require __DIR__ . '/../config.php';

$tableName = getenv('DYNAMODB_USERS_TABLE') ?: 'KuCL_Users';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $result = $dynamoClient->scan(['TableName' => $tableName]);
        $items = [];
        if (!empty($result['Items'])) {
            foreach ($result['Items'] as $item) {
                $items[] = [
                    'user_id'    => $item['user_id']['S'] ?? '',
                    'name'       => $item['name']['S'] ?? '',
                    'email'      => $item['email']['S'] ?? '',
                    'phone'      => $item['phone']['S'] ?? '',
                    'city'       => $item['city']['S'] ?? '',
                    'created_at' => $item['created_at']['S'] ?? ''
                ];
            }
        }
        echo json_encode($items);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['name'])) {
        $userId = !empty($data['user_id']) ? $data['user_id'] : bin2hex(random_bytes(8));
        
        $item = [
            'user_id'    => ['S' => $userId],
            'name'       => ['S' => $data['name']],
            'email'      => ['S' => $data['email'] ?? ''],
            'phone'      => ['S' => $data['phone'] ?? ''],
            'city'       => ['S' => $data['city'] ?? ''],
            'created_at' => ['S' => date('Y-m-d, H:i:s')]
        ];

        try {
            $dynamoClient->putItem([
                'TableName' => $tableName,
                'Item' => $item
            ]);
            echo json_encode(['status' => 'success', 'user_id' => $userId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
} elseif ($method === 'DELETE') {
    $userId = $_GET['user_id'] ?? null;
    if ($userId) {
        try {
            $dynamoClient->deleteItem([
                'TableName' => $tableName,
                'Key' => [
                    'user_id' => ['S' => $userId]
                ]
            ]);
            echo json_encode(['status' => 'deleted']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}