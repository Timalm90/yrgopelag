<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

// ------------------ START VALUES ------------------
$totalRoomCost = 0;
$totalFeatureCost = 0;

// ------------------ FETCH FORM DATA ------------------
$bookingType = $_POST['bookingType'] ?? 'room';
$selectedRoomId = isset($_POST['room']) ? (int)$_POST['room'] : null;
$arrivalInput = $_POST['arrivalDate'] ?? null;
$departureInput = $_POST['departureDate'] ?? null;
$name = $_POST['name'] ?? null;

$selectedFeatures = [];
if (isset($_POST['features'])) {
    foreach ($_POST['features'] as $featureId) {
        $selectedFeatures[] = (int)$featureId;
    };
};

// ------------------ ROOM COST ------------------
if ($bookingType === 'room' && $selectedRoomId && $arrivalInput && $departureInput) {
    // try {
    $arrivalDT = new DateTime($arrivalInput . ' 15:00');
    $departureDT = new DateTime($departureInput . ' 11:00');

    $nights = countNights($arrivalDT, $departureDT);
    if ($nights < 0) {
        $nights = 0;
    }

    $totalRoomCost = countRoomCost($pdoBooking, $selectedRoomId, $nights);
}

// ------------------ FEATURES COST ------------------
if (!empty($selectedFeatures)) {
    $totalFeatureCost = countFeatureCost($pdoBooking, $selectedFeatures);
}

// ------------------ TOTAL PRICE ------------------
$totalPrice = $totalRoomCost + $totalFeatureCost;

// //  ------------------ COMBO DISCOUNT ------------------
// Luxury room ID
$luxuryRoomId = (int)getLuxuryRoomId($pdoBooking);

// Bowser feature ID
$bowserFeatureId = (int)getBowserFeatureId($pdoBooking);

// Combo discount
$comboDiscount = getDiscount($pdoBooking, 'luxuryCombo');

if ($selectedRoomId === $luxuryRoomId && in_array($bowserFeatureId, $selectedFeatures, true)) {
    $totalPrice -= $comboDiscount;
}
// -----------------------------------------------------------------
$name = trim($name);
$isLoyal = false;
$loyalDiscount = 0;

if (isset($name) || $name !== '') {
    // Find guest
    $guestId = findGuest($pdoBooking, $name);

    if ($guestId !== null) {
        $isLoyal = checkLoyalCustomer($pdoBooking, $guestId);
        $loyalDiscount = getDiscount($pdoBooking, 'loyal');
    };
}

if ($isLoyal && $selectedRoomId === $luxuryRoomId) {
    $totalPrice -= $loyalDiscount;
};
//  ---------------------------------------------------------------

// ------------------ RETURN JSON ------------------
echo json_encode([
    'totalPrice' => $totalPrice,
    'isLoyal' => $isLoyal
]);
