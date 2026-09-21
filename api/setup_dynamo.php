<?php
require __DIR__ . '/../config.php';

$tableName = getenv('DYNAMODB_USERS_TABLE') ?: 'KuCL_Users';

try {
    $result = $dynamoClient->createTable([
        'TableName' => $tableName,
        'AttributeDefinitions' => [
            ['AttributeName' => 'user_id', 'AttributeType' => 'S']
        ],
        'KeySchema' => [
            ['AttributeName' => 'user_id', 'KeyType' => 'HASH']
        ],
        'BillingMode' => 'PAY_PER_REQUEST'
    ]);
    echo "Table '{$tableName}' is ready.\n";
} catch (Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}