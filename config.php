<?php
// Disable error output to users (check Apache error logs instead)
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;

// Helper to parse .env file directly if putenv is not configured at OS level
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

loadEnv(__DIR__ . '/.env');

$region   = getenv('AWS_REGION') ?: 'us-east-1';
$rdsHost  = getenv('RDS_HOST') ?: 'localhost';
$rdsPort  = getenv('RDS_PORT') ?: '3306';
$rdsDb    = getenv('RDS_DB_NAME') ?: 'kucl_mini_project';
$rdsUser  = getenv('RDS_USER') ?: 'admin';
$rdsPass  = getenv('RDS_PASSWORD') ?: '';

// 1. RDS (MySQL) Connection via PDO
try {
    $dsn = "mysql:host={$rdsHost};port={$rdsPort};dbname={$rdsDb};charset=utf8mb4";
    $pdo = new PDO($dsn, $rdsUser, $rdsPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection unavailable']);
    exit;
}

// 2. DynamoDB Client (Leverages EC2 Instance IAM Role automatically)
$dynamoClient = new DynamoDbClient([
    'region'  => $region,
    'version' => 'latest'
]);