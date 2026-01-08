<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$category = $data['category'] ?? null;
$item = $data['item'] ?? null;

if (!$category || !$item) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing data'
    ]);
    exit;
}

switch ($category) {
    case 'room':
        $price = getRoomPriceByName($pdo, $item);
        break;

    case 'tier':
        $price = getTierPriceByName($pdo, $item);
        break;

    default:
        echo json_encode(['success' => false]);
        exit;
}

if ($price === null) {
    echo json_encode(['success' => false]);
    exit;
}

echo json_encode([
    'success' => true,
    'price' => (int)$price
]);
exit;
