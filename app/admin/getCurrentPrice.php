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
        $stmt = $pdoBooking->prepare(
            "SELECT price_per_night FROM rooms WHERE room = :room"
        );
        $stmt->execute(['room' => $item]);
        $price = $stmt->fetchColumn();
        break;

    case 'tier':
        $stmt = $pdoBooking->prepare(
            "SELECT price_per_feature FROM tiers WHERE tier = :tier"
        );
        $stmt->execute(['tier' => $item]);
        $price = $stmt->fetchColumn();
        break;

    default:
        echo json_encode(['success' => false]);
        exit;
}

if ($price === false) {
    echo json_encode(['success' => false]);
    exit;
}

echo json_encode([
    'success' => true,
    'price' => (int)$price
]);
