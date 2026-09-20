<?php
header('Content-Type: application/json');
require __DIR__ . '/../config.php';

$tableName = getenv('DYNAMODB_USERS_TABLE') ?: 'KuCL_Users';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $dynamoClient->scan(['TableName' => $tableName]);
    $items = [];
    if (!empty($result['Items'])) {
        foreach ($result['Items'] as $item) {
            $items[] = [
                'name'       => $item['name']['S'] ?? '',
                'email'      => $item['email']['S'] ?? '',
                'phone'      => $item['phone']['S'] ?? '',
                'city'       => $item['city']['S'] ?? '',
                'created_at' => $item['created_at']['S'] ?? ''
            ];
        }
    }
    echo json_encode($items);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['name'])) {
        $dynamoClient->putItem([
            'TableName' => $tableName,
            'Item' => [
                'name'       => ['S' => $data['name']],
                'email'      => ['S' => $data['email'] ?? ''],
                'phone'      => ['S' => $data['phone'] ?? ''],
                'city'       => ['S' => $data['city'] ?? ''],
                'created_at' => ['S' => date('Y-m-d, H:i:s')]
            ]
        ]);
        echo json_encode(['status' => 'created']);
    }
} elseif ($method === 'DELETE') {
    $name = $_GET['name'] ?? null;
    if ($name) {
        $dynamoClient->deleteItem([
            'TableName' => $tableName,
            'Key' => [
                'name' => ['S' => $name]
            ]
        ]);
        echo json_encode(['status' => 'deleted']);
    }
}