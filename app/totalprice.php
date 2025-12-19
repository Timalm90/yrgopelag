<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

// ------------------ FETCH FORM DATA ------------------
$bookingType = $_POST['bookingType'] ?? 'room';
$selectedRoomId = isset($_POST['room']) ? (int)$_POST['room'] : null;
$arrivalInput = $_POST['arrivalDate'] ?? null;
$departureInput = $_POST['departureDate'] ?? null;
$selectedFeatures = $_POST['features'] ?? [];

$totalRoomCost = 0;
$totalFeatureCost = 0;
$errors = [];

// ------------------ ROOM COST ------------------
if ($bookingType === 'room' && $selectedRoomId && $arrivalInput && $departureInput) {
    try {
        $arrivalDT = new DateTime($arrivalInput . ' 15:00');
        $departureDT = new DateTime($departureInput . ' 11:00');

        $nights = countNights($arrivalDT, $departureDT);
        if ($nights < 0) {
            $errors[] = "Check your dates for arrival and departure.";
            $nights = 0;
        }

        $totalRoomCost = countRoomCost($pdo, $selectedRoomId, $nights);
    } catch (Exception $e) {
        $errors[] = "Invalid date format.";
    }
}

// ------------------ FEATURES ------------------
if (!empty($selectedFeatures)) {
    $totalFeatureCost = countFeatureCost($pdo, $selectedFeatures);
}

// ------------------ TOTAL PRICE ------------------
$totalPrice = $totalRoomCost + $totalFeatureCost;

// ------------------ RETURN JSON ------------------
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'totalPrice' => $totalPrice,
    'errors' => $errors
]);
