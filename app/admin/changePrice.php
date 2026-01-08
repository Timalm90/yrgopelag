<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

header("Content-Type: application/json");

// Fetch JSON data from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

$category = $data['category'] ?? null;
$item = $data['item'] ?? null;
$price = $data['price'] ?? null;
$mastercodeInput = $data['masterCode'] ?? null;

$errors = [];

// Validate
if (!$category || !$item || $price === null) {
    $errors[] = "Change price: Category, item and price required";
}

$category = trim($category);
$item     = trim($item);
$price    = filter_var($price, FILTER_VALIDATE_INT);

if ($price === false || $price <= 0) {
    $errors[] = "Change price: Price must be a positive integer";
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'error' => 'Change price: ' . implode('. ', $errors)
    ]);
    exit;
}

//Check mastercode
requireMastercode($mastercodeInput, $mastercode);

// Uppdate in database
switch ($category) {
    case 'room':
        updateRoomPrice($pdoBooking, $item, $price);
        break;

    case 'tier':
        updateTierPrice($pdoBooking, $item, $price);
        break;

    default:
        echo json_encode([
            'success' => false,
            'error' => "Change price: Invalid category"
        ]);
        exit;
}

$item = ucwords($item);

// Send result
echo json_encode([
    'success' => true,
    'message' => "Change price: Price for $category $item updated successfully"
]);
exit;
