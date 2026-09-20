<?php
header('Content-Type: application/json');
require __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['name']) && !empty($data['category'])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock_qty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['category'], $data['price'], $data['stock_qty']]);
        echo json_encode(['status' => 'success']);
    }
} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'deleted']);
    }
}