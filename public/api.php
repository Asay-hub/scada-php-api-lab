<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM devices WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Пристрій видалено!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Не вказано ID пристрою'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $categoryId = $input['category_id'] ?? null;
    $name = $input['name'] ?? '';
    $protocol = $input['protocol'] ?? '';

    if ($categoryId && $name && $protocol) {
        $stmt = $pdo->prepare("INSERT INTO devices (category_id, name, protocol) VALUES (:cat, :name, :prot)");
        $stmt->execute(['cat' => $categoryId, 'name' => $name, 'prot' => $protocol]);
        
        echo json_encode(['success' => true, 'message' => 'Пристрій успішно додано до бази!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Заповніть усі поля!'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$category = $_GET['category'] ?? null;

if ($category === null) {
    $stmt = $pdo->query("SELECT code FROM categories");
    echo json_encode([
        'categories' => $stmt->fetchAll(PDO::FETCH_COLUMN),
        'hint'       => 'додайте ?category=Temperature',
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$stmt = $pdo->prepare("SELECT d.id, d.name, d.protocol FROM devices d JOIN categories c ON c.id = d.category_id WHERE c.code = :category");
$stmt->execute(['category' => $category]);
$devices = $stmt->fetchAll();

if (empty($devices)) {
    http_response_code(404);
    echo json_encode(['error' => "Категорії «{$category}» немає"], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['category' => $category, 'devices' => $devices], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);