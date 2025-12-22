<?php

declare(strict_types=1);
require __DIR__ . "/autoload.php";
require __DIR__ . "/config.php";

use GuzzleHttp\Exception\RequestException;

// ------------------------------------------- START VALUES ---------------------------------------------
$totalRoomCost = 0;
$totalFeatureCost = 0;
$errors = [];
$selectedFeatures = [];
$checkinId = NULL;

// ------------------------------------------- SANITIZE & VALIDATE ---------------------------------------------
//Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $_POST['name'] === '' || $_POST['transferCode'] === '') {
    $errors[] = "Name and transferCode is mandatory!";
}

$name = trim($_POST['name']);
$transferCode = ($_POST['transferCode']);

// ------------------------------------------- FETCH FEATURES (ALL CUSTOMER TYPES) ------------------------
// Form data is always strings – cast feature IDs to int for pricing & strict comparisons
$selectedFeatures = [];
if (isset($_POST['features'])) {
    foreach ($_POST['features'] as $featureId) {
        $selectedFeatures[] = (int) $featureId;
    }
}

// ------------------------------------------- VALIDATE FEATURE-ONLY REQUIREMENTS ------------------------
if (!isset($_POST['room']) && !empty($selectedFeatures)) {
    if (!isset($_POST['arrivalDate']) || $_POST['arrivalDate'] === '') {
        $errors[] = "Date is required when booking features only.";
    }
}

// ---------------------------------------- FIND/REGISTER GUEST IN DB ------------------------------------------
// Find guest in DB if already exists
$guestId = findGuest($pdoBooking, $name);

// If not exists, register and fetch ID
if ($guestId === NULL) {
    registerGuest($pdoBooking, $name);
    $guestId = findGuest($pdoBooking, $name);
}

// ------------------------------------------- BOOK ROOM ---------------------------------------------
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = (int) $_POST['room'];

    // Convert to DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' 15:00');
    $departureDT = new DateTime($_POST['departureDate'] . ' 11:00');

    // Check if avaiable
    $occupiedDates = checkAvailable($pdoBooking, $selectedRoomId);
    $isAvailable = true;

    // Loop through all booked dates
    foreach ($occupiedDates as $booked) {
        $bookedArrival = new DateTime($booked['arrival']);
        $bookedDeparture = new DateTime($booked['departure']);
        if ($arrivalDT < $bookedDeparture && $departureDT > $bookedArrival) {
            $isAvailable = false;
            break;
        }
    }

    if (!$isAvailable) {
        $errors[] = "Room is not available on chosen dates.";
    }

    // Count number of nights
    $nights = countNights($arrivalDT, $departureDT);
    if ($nights < 0) {
        $errors[] = "Check your dates for arrival and departure.";
        $nights = 0;
    }

    // Calculate total Room cost
    $totalRoomCost = countRoomCost($pdoBooking, $selectedRoomId, $nights);
}

// ------------------------------------------- FEATURE-ONLY DATES ---------------------------------------------
if (!isset($selectedRoomId) && isset($_POST['arrivalDate'])) {
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' 15:00');
    $departureDT = clone $arrivalDT; // Mandatory in receipt
}

// ------------------------------------------- PAYMENT FEATURES ---------------------------------------------
if (!empty($selectedFeatures)) {
    // Calculate total cost for all selected features
    $totalFeatureCost = countFeatureCost($pdoBooking, $selectedFeatures);
}

// ------------------------------------------- TOTAL PRICE ---------------------------------------------
$totalPrice = $totalRoomCost + $totalFeatureCost;

// -------------------- PREPARE DISCOUNT -----------------------
// Fetch luxury room:
// $luxury = $pdoBooking->prepare("SELECT id FROM rooms WHERE room = 'luxury'");
// $luxury->execute();
// $luxuryRoomId = (int) $luxury->fetch(PDO::FETCH_ASSOC)['id'];
$luxuryRoomId = getLuxuryRoomId($pdoBooking);


// Fetch feature for discount
// $bowserFeature = $pdoBooking->prepare("SELECT id FROM features WHERE feature = 'Bowser’s Castle Escape'");
// $bowserFeature->execute();
// $bowserRow = $bowserFeature->fetch(PDO::FETCH_ASSOC);
// $bowserFeatureId = (int) $bowserRow['id'];
$bowserFeatureId = getBowserFeatureId($pdoBooking);


// -------------------- DISCOUNTS -----------------------
// Loyal customer:
// $isLoyal = $pdoBooking->prepare("SELECT guest_id, COUNT(guest_id) AS visits FROM checkins WHERE guest_id = :guestId GROUP BY guest_id");
// $isLoyal->bindParam(":guestId", $guestId, PDO::PARAM_INT);
// $isLoyal->execute();
// $isLoyal = $isLoyal->fetch(PDO::FETCH_ASSOC);
$isLoyal = checkLoyalCustomer($pdoBooking, $guestId);

// Fetch discount from DB:
// $loyalDiscountStmt = $pdoAdmin->prepare("SELECT discount FROM discounts WHERE type = 'loyal'");
// $loyalDiscountStmt->execute();
// $loyalDiscountRow = $loyalDiscountStmt->fetch(PDO::FETCH_ASSOC);
// $loyalDiscount = (int) $loyalDiscountRow['discount'];
$loyalDiscount = getDiscount($pdoAdmin, 'loyal');
$comboDiscount = getDiscount($pdoAdmin, 'luxuryCombo');

// Loyal offer:
if ($isLoyal && (int)$isLoyal['visits'] >= 1 && isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId) {
    $totalPrice -= $loyalDiscount;
}

// Combo offer:
if (isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId && in_array($bowserFeatureId, $selectedFeatures, true)) {
    $totalPrice -= $comboDiscount;
}

// ------------------------------------------- VALIDATE TRANSFERCODE ---------------------------------------------
try {
    $transferCodeResponse = $client->post('/centralbank/transferCode', [
        'json' => [
            'transferCode' => $transferCode,
            'totalCost'    => $totalPrice
        ]
    ]);

    $transferCodeResult = json_decode($transferCodeResponse->getBody()->getContents(), true);

    if (!isset($transferCodeResult['status']) || $transferCodeResult['status'] !== 'success') {
        $errors[] = htmlspecialchars(getErrorMessage($transferCodeResult['error']));
    }
} catch (RequestException $transferCodeException) {
    if ($transferCodeException->hasResponse()) {
        $apiError = json_decode($transferCodeException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        $errors[] = htmlspecialchars(getErrorMessage($apiError));
    } else {
        $errors[] = htmlspecialchars("Could not reach the payment system. Please try again later.");
    }
}

handleErrors($errors);


// ------------------------------------------- PREPARE FEATURES FOR RECEIPT ---------------------------------------------
$featuresUsed = prepareFeaturesForReceipt($pdoBooking, $selectedFeatures);

// $featuresUsed = [];

// // Fetch name on 4th category
// $specificCategory = $pdoBooking->prepare("SELECT category FROM categories WHERE id = 4");
// $specificCategory->execute();
// $specificCategory = $specificCategory->fetch(PDO::FETCH_ASSOC)['category'];

// $statementReceipt = $pdoBooking->prepare("SELECT categories.category, tiers.tier FROM features INNER JOIN categories ON features.category_id = categories.id INNER JOIN tiers ON features.tier_id = tiers.id WHERE features.id = :id");

// // For every choosen feature, find its cateogry and tier level. If hotel specific category, rename it to "hotel-specific"
// foreach ($selectedFeatures as $featureId) {
//     $statementReceipt->bindParam(":id", $featureId, PDO::PARAM_INT);
//     $statementReceipt->execute();
//     $dbRow = $statementReceipt->fetch(PDO::FETCH_ASSOC);

//     if ($dbRow['category'] === $specificCategory) {
//         $dbRow['category'] = "hotel-specific";
//     }

//     $featuresUsed[] = [
//         'activity' => $dbRow['category'],
//         'tier'     => $dbRow['tier']
//     ];
// }

// ------------------------------------------- RECEIPT ---------------------------------------------

// Create a receipt and send to centralbank
try {
    $receiptResponse = $client->post('/centralbank/receipt', [
        'json' => [
            'user'           => "Emilie",
            'api_key'        => $apiKey,
            'guest_name'     => $name,
            'arrival_date'   => $arrivalDT->format('Y-m-d'),
            'departure_date' => $departureDT->format('Y-m-d'),
            'features_used'  => $featuresUsed,
            'star_rating'    => 5
        ]
    ]);

    $receiptResult = json_decode(
        $receiptResponse->getBody()->getContents(),
        true
    );

    if (!isset($receiptResult['status']) || $receiptResult['status'] !== 'success') {
        $errors[] = htmlspecialchars(getErrorMessage($receiptResult['error']));
    }
} catch (RequestException $receiptException) {
    if ($receiptException->hasResponse()) {
        $apiError = json_decode($receiptException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        $errors[] = htmlspecialchars(getErrorMessage($apiError));
    } else {
        $errors[] = "Could not complete the payment. Please try again later.";
    }
}

// if (!empty($errors)) {
//     $_SESSION['errors'] = $errors;
//     header("Location: /../index.php");
//     exit;
// }
handleErrors($errors);



// ------------------------------------ REQUEST CENTRALBANK DEPOSIT --------------------------------------
// Make a request to centralbank to make a deposit
try {
    $depositResponse = $client->post('/centralbank/deposit', [
        'json' => [
            'user'         => "Emilie",
            'transferCode' => $transferCode
        ]
    ]);

    $depositResult = json_decode($depositResponse->getBody()->getContents(), true);

    if (!isset($depositResult['status']) || $depositResult['status'] !== 'success') {
        $errors[] = $depositResult['error'];
    }
} catch (RequestException $depositException) {
    if ($depositException->hasResponse()) {
        $apiError = json_decode($depositException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        $errors[] = getErrorMessage($apiError);
    } else {
        $errors[] = "Due to technical issues, the payment could not be completed. Please try again later.";
    }
}

// if (!empty($errors)) {
//     // Store errors in session to display in UI
//     $_SESSION['errors'] = $errors;
//     header("Location: /../index.php");
//     exit;
// }
handleErrors($errors);

// -------------------------------------- REGISTER BOOKED ROOM IN DB ----------------------------------------
// Requires: guest_id, room_id, arrival & departure in checkins for room
if (isset($selectedRoomId, $arrivalDT, $departureDT)) {
    roomCheckin($pdoBooking, $guestId, $selectedRoomId, $arrivalDT, $departureDT);
    $checkinId = findCheckinId($pdoBooking, $guestId, $arrivalDT);
}

// ------------------------------------ REGISTER FEATURE-ONLY CUSTOMERS --------------------------------------
// Requires: guest_id, arrival --> customer gets checkin_id (used for registering features)
if (!isset($selectedRoomId) && isset($arrivalDT)) {
    if (empty($selectedFeatures)) {
        $errors[] = "You must select at least one feature!";
        // $_SESSION['errors'] = $errors;
        // header("Location: /../index.php");
        // exit;
        handleErrors($errors);
    }
    featureOnlyCheckin($pdoBooking, $guestId, $arrivalDT);
    $checkinId = findCheckinId($pdoBooking, $guestId, $arrivalDT);
}

// -------------------------------------- REGISTER BOOKED FEATURE IN DB ----------------------------------------
//Requires: checkin_id, choosen feature_id
if (!empty($selectedFeatures) && $checkinId !== NULL) {
    // Register chosen features on checkin_id
    registerFeatures($pdoBooking, $checkinId, $selectedFeatures);
}

// ----------------------------------------- USER CONFIRMATION -------------------------------------------

// ------------------ DETERMINE BOOKING TYPE ------------------
if (isset($selectedRoomId) && !empty($selectedFeatures)) {
    $bookingType = 'Room with features';
} elseif (isset($selectedRoomId)) {
    $bookingType = 'Room';
} else {
    $bookingType = 'Day pass';
}

$featureNames = [];
foreach ($selectedFeatures as $featureId) {
    // $stmt = $pdoBooking->prepare("SELECT feature FROM features WHERE id = :id");
    // $stmt->bindParam(':id', $featureId, PDO::PARAM_INT);
    // $stmt->execute();
    // $featureRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $featureRow = getFeatureName($pdoBooking, $featureId);
    if ($featureRow !== NULL) {
        $featureNames[] = $featureRow;
    }
}

// ------------------------------------------- PREPARE CONFIRMATION DATA ---------------------------------------------
$confirmation = [
    'visitor'      => $name,
    'bookingType'  => $bookingType, // "Room", "Room with features", "Day pass"
    'arrival'      => $arrivalDT->format('Y-m-d'),
    'departure'    => $departureDT->format('Y-m-d'),
    'checkinTime'  => '15:00',
    'checkoutTime' => '11:00',
    'roomName'     => isset($selectedRoomId) ? getRoomName($pdoBooking, $selectedRoomId) : null,
    'features'     => $featureNames, // Array w feature names
    'totalcost'    => $totalPrice,
    'discountSum'  => ($loyalDiscountApplied ?? 0) + ($comboDiscountApplied ?? 0)
];

$_SESSION['success'] = $confirmation;
header("Location: /../index.php");
exit;
