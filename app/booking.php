<?php

declare(strict_types=1);
require __DIR__ . "/autoload.php"; // For $client $pdo
require __DIR__ . "/config.php"; // For API-key 
// Also nned require vendor/autoload for guzzle to work?!

use GuzzleHttp\Exception\RequestException;

// ------------------------------------------- ERROR HANDLING ---------------------------------------------
$errors = [];

// START VALUES
$totalRoomCost = 0; // STARTVÄRDE FÖR SÄKERHET
$totalFeatureCost = 0; // STARTVÄRDE FÖR SÄKERHET
$selectedFeatures = []; // STARTVÄRDE
$checkinId = NULL;

// ------------------------------------------- SANITIZE & VALIDATE ---------------------------------------------
// Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $_POST['name'] === '' || $_POST['transferCode'] === '') {
    $errors[] = "Name and transferCode is mandatory!";
}

// Sanitize & validate inputs, prevent XSS
$name = sanitizeString(trim($_POST['name']));
$transferCode = sanitizeString($_POST['transferCode']);

// ------------------------------------------- BOOK ROOM ---------------------------------------------
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = $_POST['room'];

    // Convert to DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($_POST['arrivalDate'] . ' 15:00');
    $departureDT = new DateTime($_POST['departureDate'] . ' 11:00');

    // ------------------------------------------- AVAILABLE? ---------------------------------------------
    $occupiedDates = checkAvailable($pdo, $selectedRoomId);

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
    //Count number of nights
    // $nights = 0;

    // $arrivalDay   = (int)$arrivalDT->format('j');   // -> Day as int
    // $departureDay = (int)$departureDT->format('j'); // -> Day as int

    // if ($departureDay < $arrivalDay) {
    //     $errors[] = "Check your dates for arrival and departure.";
    // }

    // // Number of nights
    // $nights = $departureDay - $arrivalDay;

    // Count number of nights
    $nights = countNights($arrivalDT, $departureDT);
    if ($nights < 0) {
        $errors[] = "Check your dates for arrival and departure.";
        $nights = 0;
    }

    // Fetch price per night for ALL rooms
    // $pdoAllRooms = $pdo->prepare("SELECT * FROM rooms");
    // $pdoAllRooms->execute();
    // $allRooms = $pdoAllRooms->fetchAll(PDO::FETCH_ASSOC);

    // // Create array: room name => price_per_night
    // $roomPrices = [];
    // foreach ($allRooms as $room) {
    //     $roomPrices[$room['id']] = (int)$room['price_per_night'];
    // };
    // $roomPrices = getRoomPrices($pdo);

    // Count total price for choosen room:
    // $totalRoomCost = 0;
    $totalRoomCost = countRoomCost($pdo, $selectedRoomId, $nights);
    // $pricePerNight = $roomPrices[$selectedRoomId];
    // $totalRoomCost = $pricePerNight * $nights;
};

// ------------------------------------------- BOOK FEATURE ---------------------------------------------
if (isset($_POST['features'], $_POST['arrivalDate'])) {
    // ------------------------------------------- PAYMENT FEATURE ---------------------------------------------
    // Fetch selected features from form
    $selectedFeatures = $_POST['features'] ?? [];

    // Fetch price for each feature in DB:
    // $featurePrices = getFeaturePrices($pdo);

    // $totalFeatureCost = 0;
    // Takes each selected Feature, find the price in price list, and sums it up in totalFeatureCost
    $totalFeatureCost = countFeatureCost($pdo, $selectedFeatures);

    // foreach ($selectedFeatures as $featureId) {
    //     if (isset($featurePrices[$featureId])) {
    //         $totalFeatureCost += $featurePrices[$featureId];
    //     };
    // };
};

// ------------------------------------------- TOTAL PRICE ---------------------------------------------

$totalPrice = $totalRoomCost + $totalFeatureCost;

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
            'features_used'  => $selectedFeatures,
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
// ---------------------------------------- FIND/REGISTER GUEST IN DB ------------------------------------------
// Find guest in DB if already exists
$guestId = findGuest($pdo, $name);

if ($guestId === NULL) {
    registerGuest($pdo, $name);
    $guestId = findGuest($pdo, $name);
};

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
    roomCheckin($pdo, $guestId, $selectedRoomId, $arrivalDT, $departureDT);
    $checkinId = findCheckinId($pdo, $guestId, $arrivalDT);
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

    featureOnlyCheckin($pdo, $guestId, $arrivalDT);
    $checkinId = findCheckinId($pdo, $guestId, $arrivalDT);
}


// -------------------------------------- REGISTER BOOKED FEATURE IN DB ----------------------------------------
//Requires: checkin_id, choosen feature_id
if (!empty($selectedFeatures) && $checkinId !== NULL) {
    // // Fetch checkin_id
    // $checkinId = findCheckinId($pdo, $guestId, $arrivalDT);

    // Register chosen features on checkin_id
    registerFeatures($pdo, $checkinId, $selectedFeatures);
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
$_SESSION['success'] = "Booking completed successfully! Your room and features are confirmed.";

// Send user back to start page
header("Location: /../index.php");
exit;
