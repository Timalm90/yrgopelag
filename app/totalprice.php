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
// $discount = 0; // Discount test
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

        $totalRoomCost = countRoomCost($pdoBooking, $selectedRoomId, $nights);
    } catch (Exception $e) {
        $errors[] = "Invalid date format.";
    }
}

// ------------------ FEATURES COST ------------------
if (!empty($selectedFeatures)) {
    $totalFeatureCost = countFeatureCost($pdoBooking, $selectedFeatures);
}

// // //DISCOUNT TEST...
// //  ------------------ APPLY DISCOUNT ------------------

// // PREPARE DISCOUNT DATA

// // Fetch luxury room id
// $luxuryRoom = $pdoBooking->prepare("SELECT id FROM rooms WHERE room = 'luxury'");
// $luxuryRoom->execute();
// $luxuryRoom = $luxuryRoom->fetch(PDO::FETCH_ASSOC);
// $luxuryRoomId = $luxuryRoom['id']; // 3

// // Fetch Bowser feature id
// $bowserId = $pdoBooking->prepare('SELECT id FROM features WHERE feature LIKE "Bowser%"');
// $bowserId->execute();
// $bowserId = $bowserId->fetch(PDO::FETCH_ASSOC);
// $bowserFeatureId = $bowserId['id']; // 16

// // Fetch combo discount
// $discountCombo = $pdoAdmin->prepare(
//     "SELECT discount FROM discounts WHERE type = 'luxuryCombo'"
// );
// $discountCombo->execute();
// $comboDiscount = $discountCombo->fetch(PDO::FETCH_ASSOC);
// $comboDiscount = $comboDiscount['discount']; // 5

// if (isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId && in_array($bowserFeatureId, $selectedFeatures, true)) {
//     $discount = $comboDiscount;
// };

// ------------------ TOTAL PRICE ------------------
$totalPrice = $totalRoomCost + $totalFeatureCost;

// ------------------ RETURN JSON ------------------
echo json_encode([
    'totalPrice' => $totalPrice,
    'errors' => $errors
]);
