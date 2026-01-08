<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

// ----- START VALUES -----
$totalRoomCost = 0;
$totalFeatureCost = 0;

// ----- FETCH FORM DATA -----
$bookingType = $_POST['bookingType'] ?? 'room';
$selectedRoomId = isset($_POST['room']) ? (int)$_POST['room'] : null;
$arrivalInput = $_POST['arrivalDate'] ?? null;
$departureInput = $_POST['departureDate'] ?? null;
$name = $_POST['name'] ?? null;

// Convert feature IDs to int
$selectedFeatures = [];
if (isset($_POST['features'])) {
    foreach ($_POST['features'] as $featureId) {
        $selectedFeatures[] = (int)$featureId;
    };
};

// ----- ROOM COST -----
if ($bookingType === 'room' && $selectedRoomId && $arrivalInput && $departureInput) {
    $arrivalDT = new DateTime($arrivalInput . ' ' . $checkinTime);
    $departureDT = new DateTime($departureInput . ' ' . $checkoutTime);

    $nights = countNights($arrivalDT, $departureDT);
    if ($nights < 0) {
        $nights = 0;
    }

    $totalRoomCost = countRoomCost($pdo, $selectedRoomId, $nights);
}

// ----- FEATURES COST -----
if (!empty($selectedFeatures)) {
    $totalFeatureCost = countFeatureCost($pdo, $selectedFeatures);
}

// ----- TOTAL PRICE -----
$totalPrice = $totalRoomCost + $totalFeatureCost;

// ----- DISCOUNTS -----
$name = trim($name);
$isLoyal = false;

// Combo Discount
if ($selectedRoomId === $luxuryRoomId && in_array($bowserFeatureId, $selectedFeatures, true)) {
    $totalPrice -= $comboDiscount;
}

// Loyal discount
if (isset($name) || $name !== '') {
    // Find guest
    $guestId = findGuest($pdo, $name);

    if ($guestId !== null) {
        $isLoyal = checkLoyalCustomer($pdo, $guestId);
    };
}

if ($isLoyal && $selectedRoomId === $luxuryRoomId) {
    $totalPrice -= $loyalDiscount;
};

// Ensure total price is not negative
if ($totalPrice < 0) {
    $totalPrice = 0;
}

// ----- RETURN JSON -----
echo json_encode([
    'totalPrice' => $totalPrice,
    'isLoyal' => $isLoyal
]);
