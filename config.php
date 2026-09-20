<?php
require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;

$region   = getenv('AWS_REGION') ?: 'us-east-1';
$rdsHost  = getenv('RDS_HOST') ?: 'localhost';
$rdsDb    = getenv('RDS_DB_NAME') ?: 'kucl_mini_project';
$rdsUser  = getenv('RDS_USER') ?: 'admin';
$rdsPass  = getenv('RDS_PASSWORD') ?: '';

// 1. RDS MySQL Connection
try {
    $pdo = new PDO("mysql:host={$rdsHost};dbname={$rdsDb};charset=utf8mb4", $rdsUser, $rdsPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'RDS Connection failed: ' . $e->getMessage()]);
    exit;
}

// 2. DynamoDB Client (EC2 IAM role credentials)
$dynamoClient = new DynamoDbClient([
    'region'  => $region,
    'version' => 'latest'
]);