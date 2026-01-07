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
$bookingId = NULL;
$starRating = (int)$starRating; // int 5

// ------------------------------------------- SANITIZE & VALIDATE ---------------------------------------------
//Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $_POST['name'] === '' || $_POST['transferCode'] === '') {
    $errors[] = "Name and transferCode is mandatory!";
}

$name = trim($_POST['name']);
$transferCode = ($_POST['transferCode']);

// ------------------------------------------- FETCH FEATURES (ALL CUSTOMER TYPES) ------------------------
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
    if ($guestId === NULL) {
        $errors[] = "Could not register guest. Please try again.";
        handleErrors($errors);
    }
}

// ------------------------------------------- BOOK ROOM ---------------------------------------------
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = (int) $_POST['room'];

    // Convert to DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' ' . $checkinTime);
    $departureDT = new DateTime($_POST['departureDate'] . ' ' . $checkoutTime);

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
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' ' . $checkinTime);
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
$isLoyal = checkLoyalCustomer($pdoBooking, $guestId);

// Loyal offer:
if ($isLoyal && isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId) {
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

// ------------------------------------------- RECEIPT ---------------------------------------------

// Create a receipt and send to centralbank
try {
    $receiptResponse = $client->post('/centralbank/receipt', [
        'json' => [
            'user'           => $hotelOwner,
            'api_key'        => $apiKey,
            'guest_name'     => $name,
            'arrival_date'   => $arrivalDT->format('Y-m-d'),
            'departure_date' => $departureDT->format('Y-m-d'),
            'features_used'  => $featuresUsed,
            'star_rating'    => $starRating
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

handleErrors($errors);

// ------------------------------------ REQUEST CENTRALBANK DEPOSIT --------------------------------------
// Make a request to centralbank to make a deposit
try {
    $depositResponse = $client->post('/centralbank/deposit', [
        'json' => [
            'user'         => $hotelOwner,
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

handleErrors($errors);

// -------------------------------------- REGISTER BOOKED ROOM IN DB ----------------------------------------
if (isset($selectedRoomId, $arrivalDT, $departureDT)) {
    roomBooking($pdoBooking, $guestId, $selectedRoomId, $arrivalDT, $departureDT);
    $bookingId = findBookingId($pdoBooking, $guestId, $arrivalDT);
}

// ------------------------------------ REGISTER FEATURE-ONLY CUSTOMERS --------------------------------------
if (!isset($selectedRoomId) && isset($arrivalDT)) {
    if (empty($selectedFeatures)) {
        $errors[] = "You must select at least one feature!";
        handleErrors($errors);
    }
    featureOnlyBooking($pdoBooking, $guestId, $arrivalDT);
    $bookingId = findBookingId($pdoBooking, $guestId, $arrivalDT);
}

// -------------------------------------- REGISTER BOOKED FEATURE IN DB ----------------------------------------
if (!empty($selectedFeatures) && $bookingId !== NULL) {
    // Register chosen features on checkin_id
    registerFeatures($pdoBooking, $bookingId, $selectedFeatures);
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
    $featureRow = getFeatureName($pdoBooking, $featureId);
    if ($featureRow !== NULL) {
        $featureNames[] = $featureRow;
    }
}

// ------------------------------------------- PREPARE CONFIRMATION DATA ---------------------------------------------
$confirmation = [
    'visitor'      => $name,
    'bookingType'  => $bookingType,
    'arrival'      => $arrivalDT->format('Y-m-d'),
    'departure'    => $departureDT->format('Y-m-d'),
    'checkinTime'  => $checkinTime,
    'checkoutTime' => $checkoutTime,
    'roomName'     => isset($selectedRoomId) ? getRoomName($pdoBooking, $selectedRoomId) : null,
    'features'     => $featureNames,
    'totalcost'    => $totalPrice,
    'discountSum'  => ($loyalDiscountApplied ?? 0) + ($comboDiscountApplied ?? 0)
];

$_SESSION['success'] = $confirmation;
header("Location: ../index.php");
exit;
