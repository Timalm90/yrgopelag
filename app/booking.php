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
// Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $_POST['name'] === '' || $_POST['transferCode'] === '') {
    $errors[] = "Name and transferCode is mandatory!";
}

// Sanitize & validate inputs, prevent XSS
$name = sanitizeString(trim($_POST['name']));
$transferCode = sanitizeString($_POST['transferCode']);

// ---------------------------------------- FIND/REGISTER GUEST IN DB ------------------------------------------
// Find guest in DB if already exists
$guestId = findGuest($pdoBooking, $name);

if ($guestId === NULL) {
    registerGuest($pdoBooking, $name);
    $guestId = findGuest($pdoBooking, $name);
};

// ------------------------------------------- BOOK ROOM ---------------------------------------------
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = (int) $_POST['room'];

    // Convert to DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' 15:00');
    $departureDT = new DateTime($_POST['departureDate'] . ' 11:00');

    // ------------------------------------------- AVAILABLE? ---------------------------------------------
    $occupiedDates = checkAvailable($pdoBooking, $selectedRoomId);

    $isAvailable = true;

    // Loop through all booked dates
    foreach ($occupiedDates as $booked) {
        $bookedArrival   = new DateTime($booked['arrival']);
        $bookedDeparture = new DateTime($booked['departure']);

        if ($arrivalDT < $bookedDeparture && $departureDT > $bookedArrival) {
            $isAvailable = false;
            break;
        }
    }

    if (!$isAvailable) {
        $errors[] = "Room is not available on chosen dates.";
    }

    // ---------------------------------------- PAYMENT HOTELL ROOM ------------------------------------------
    $nights = countNights($arrivalDT, $departureDT);
    if ($nights < 0) {
        $errors[] = "Check your dates for arrival and departure.";
        $nights = 0;
    }

    $totalRoomCost = countRoomCost($pdoBooking, $selectedRoomId, $nights);
};

// ------------------------------------------- BOOK FEATURE ---------------------------------------------
if (isset($_POST['features'], $_POST['arrivalDate'])) {
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' 15:00');
    $departureDT = clone $arrivalDT; // Mandatory in receipt
    // ------------------------------------------- PAYMENT FEATURE ---------------------------------------------
    // Fetch selected features from form
    $selectedFeatures = $_POST['features'] ?? [];

    // [THIS NEEDS A GOOD EXPLAINING COMMENT]
    $totalFeatureCost = countFeatureCost($pdoBooking, $selectedFeatures);
};

// ------------------------------------------- TOTAL PRICE ---------------------------------------------

$totalPrice = $totalRoomCost + $totalFeatureCost;

// -------------------- PREPARE DISCOUNT -----------------------
// Fetch luxury room:
$luxury = $pdoBooking->prepare("SELECT id FROM rooms WHERE room = 'luxury'");
$luxury->execute();
$luxury = $luxury->fetch(PDO::FETCH_ASSOC);
$luxuryRoomId = (int) $luxury['id'];

// Fetch feature for discount
$bowserFeature = $pdoBooking->prepare(
    "SELECT id FROM features WHERE feature = 'Bowser’s Castle Escape'"
);
$bowserFeature->execute();
$bowserFeature = $bowserFeature->fetch(PDO::FETCH_ASSOC);

$bowserFeatureId = (int) $bowserFeature['id'];

// -------------------- DISCOUNT LOYAL -----------------------
// Loyal customer:
$isLoyal = $pdoBooking->prepare("SELECT guest_id, COUNT(guest_id) AS visits FROM checkins WHERE guest_id = :guestId GROUP BY guest_id");
$isLoyal->bindParam(":guestId", $guestId, PDO::PARAM_INT);
$isLoyal->execute();
$isLoyal = $isLoyal->fetch(PDO::FETCH_ASSOC);

// Fetch discount from DB:
$loyalDiscount = $pdoAdmin->prepare("SELECT * FROM discounts WHERE type = 'loyal'");
$loyalDiscount->execute();
$loyalDiscount = $loyalDiscount->fetch(PDO::FETCH_ASSOC);
$loyalDiscount = $loyalDiscount['discount'];

// If customer is returning AND has booked luxury Room, get loyal-discount. Find another placeholder for luxury room id 3?
if ($isLoyal && (int)$isLoyal['visits'] >= 1 && isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId) {
    $totalPrice = $totalPrice - (int)$loyalDiscount;
};

// -------------------- DISCOUNT LUXURY COMBO -----------------------

// If customer chooses luxury room AND Bowser’s Castle Escape, get LuxuryCombo discount:

// Fetch discount from DB:
$comboDiscount = $pdoAdmin->prepare("SELECT * FROM discounts WHERE type = 'luxuryCombo'");
$comboDiscount->execute();
$comboDiscount = $comboDiscount->fetch(PDO::FETCH_ASSOC);
$comboDiscount = $comboDiscount['discount'];

if (isset($selectedRoomId) && $selectedRoomId === $luxuryRoomId && in_array($bowserFeatureId, $selectedFeatures, true)) {
    $totalPrice = $totalPrice - (int)$comboDiscount;
};

// ------------------------------------------- VALIDATE TRANSFERCODE ---------------------------------------------
try {
    $transferCodeResponse = $client->post('/centralbank/transferCode', [
        'json' => [
            'transferCode' => $transferCode,
            'totalCost'    => $totalPrice
        ]
    ]);

    $transferCodeResult = json_decode(
        $transferCodeResponse->getBody()->getContents(),
        true
    );

    if (
        !isset($transferCodeResult['status']) ||
        $transferCodeResult['status'] !== 'success'
    ) {
        $errors[] = $transferCodeResult['error']
            ?? "TransferCode validation failed.";
    }
} catch (RequestException $transferCodeException) {
    $errors[] = $transferCodeException->getMessage();
}


// ------------------------------------------- PREP FEATURES FOR RECEIPT ---------------------------------------------
$featuresUsed = [];

$specificCategory = $pdoBooking->prepare("SELECT category FROM categories WHERE id = 4");
$specificCategory->execute();
$specificCategory = $specificCategory->fetch(PDO::FETCH_ASSOC);
$specificCategory = $specificCategory['category'];

$statementReceipt = $pdoBooking->prepare("SELECT categories.category, tiers.tier FROM features INNER JOIN categories ON features.category_id = categories.id INNER JOIN tiers ON features.tier_id = tiers.id WHERE features.id = :id");

foreach ($selectedFeatures as $featureId) {
    $statementReceipt->bindParam(":id", $featureId, PDO::PARAM_INT);
    $statementReceipt->execute();
    $dbRow = $statementReceipt->fetch(PDO::FETCH_ASSOC);

    // Mario-themed -> hotel-specific
    if ($dbRow['category'] === $specificCategory) {
        $dbRow['category'] = "hotel-specific";
    };

    $featuresUsed[] = [
        'activity' => $dbRow['category'],
        'tier'     => $dbRow['tier']
    ];
}

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

    if (!isset($receiptResult['status'])) {
        $errors[] = $receiptResult['error']
            ?? "Receipt registration failed.";
    }
} catch (RequestException $receiptException) {
    $errors[] = $receiptException->getMessage();
}


// ------------------------------------------- REGISTER IN DB ---------------------------------------------
// Moved find/register guest higher up, to be able to implement discount to returning guests!

// ------------------------------------------- ERROR HANDLING ---------------------------------------------
if (!empty($errors)) {
    // Optionally store errors in session to display in UI
    $_SESSION['errors'] = $errors;
    header("Location: /../index.php");
    exit;
}

// -------------------------------------- REGISTER BOOKED ROOM IN DB ----------------------------------------
// Requires: guest_id, room_id, arrival & departure in checkins for room
if (isset($selectedRoomId, $arrivalDT, $departureDT)) {
    roomCheckin($pdoBooking, $guestId, $selectedRoomId, $arrivalDT, $departureDT);
    $checkinId = findCheckinId($pdoBooking, $guestId, $arrivalDT);
}
// ------------------------------------ REGISTER FEATURE-ONLY-CUSTOMERS --------------------------------------
// Requires: guest_id, arrival --> customer gets checkin_id (used for registering features)
if (!isset($selectedRoomId) && isset($arrivalDT)) {

    // Check that at least 1 feature is choosen, doesn't want to register empty checkins
    if (empty($selectedFeatures)) {
        $errors[] = "You must select at least one feature!";
        $_SESSION['errors'] = $errors;
        header("Location: /../index.php");
        //Stop script
        exit;
    }

    featureOnlyCheckin($pdoBooking, $guestId, $arrivalDT);
    $checkinId = findCheckinId($pdoBooking, $guestId, $arrivalDT);
}


// -------------------------------------- REGISTER BOOKED FEATURE IN DB ----------------------------------------
//Requires: checkin_id, choosen feature_id
if (!empty($selectedFeatures) && $checkinId !== NULL) {
    // Register chosen features on checkin_id
    registerFeatures($pdoBooking, $checkinId, $selectedFeatures);
};

// ------------------------------------ REQUEST CENTRALBANK DEPOSIT --------------------------------------
// Make a request to centralbank to make a deposit
try {
    $depositResponse = $client->post('/centralbank/deposit', [
        'json' => [
            'user'         => "Emilie",
            'transferCode' => $transferCode
        ]
    ]);

    $depositResult = json_decode(
        $depositResponse->getBody()->getContents(),
        true
    );

    if (
        !isset($depositResult['status']) ||
        $depositResult['status'] !== 'success'
    ) {
        $errors[] = $depositResult['error']
            ?? "Deposit failed.";
    }
} catch (RequestException $depositException) {
    $errors[] = $depositException->getMessage();
}

// ----------------------------------------- USER CONFIRMATION -------------------------------------------
// Show confirmation message to user!
$confirmation = [
    'visitor' => $name,
    'arrival' => $arrivalDT->format('Y-m-d'),
    'departure' => $departureDT->format('Y-m-d'),
    'features' => $selectedFeatures,
    'totalcost' => $totalPrice
];

$_SESSION['success'] = $confirmation;

// Send user back to start page
header("Location: /../index.php");
exit;
