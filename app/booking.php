<?php

declare(strict_types=1);
require __DIR__ . "/autoload.php"; // For $client $pdo
require __DIR__ . "/config.php"; // For API-key 
// Also nned require vendor/autoload for guzzle to work?!

use GuzzleHttp\Exception\RequestException;

// ------------------------------------------- ERROR HANDLING ---------------------------------------------
$errors = [];

// ------------------------------------------- SANITIZE & VALIDATE ---------------------------------------------
// Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $_POST['name'] === '' || $_POST['transferCode'] === '') {
    $errors[] = "Name and transferCode is mandatory!";
}

// Sanitize & validate inputs, prevent XSS
$name = htmlspecialchars(trim($_POST['name']));
$transferCode = htmlspecialchars($_POST['transferCode']);

// ------------------------------------------- BOOK ROOM ---------------------------------------------
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = $_POST['room'];
    $arrivalInput = $_POST['arrivalDate'];
    $departureInput = $_POST['departureDate'];

    // Convert to DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($arrivalInput . ' 15:00');
    $departureDT = new DateTime($departureInput . ' 11:00');

    // ------------------------------------------- AVAILABLE? ---------------------------------------------
    $occupiedDates = $pdo->prepare("SELECT arrival, departure FROM checkins WHERE room_id = :room_id");
    $occupiedDates->bindParam(":room_id", $selectedRoomId, PDO::PARAM_INT);
    $occupiedDates->execute();
    $occupiedDates = $occupiedDates->fetchAll(PDO::FETCH_ASSOC);

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
    $nights = 0;

    $arrivalDay   = (int)$arrivalDT->format('j');   // -> Day as int
    $departureDay = (int)$departureDT->format('j'); // -> Day as int

    if ($departureDay < $arrivalDay) {
        $errors[] = "Check your dates for arrival and departure.";
    }

    // Number of nights
    $nights = $departureDay - $arrivalDay;

    // Fetch price per night for ALL rooms
    $pdoAllRooms = $pdo->prepare("SELECT * FROM rooms");
    // Risk of XSS? Only fetching price per night...
    $pdoAllRooms->execute();
    $allRooms = $pdoAllRooms->fetchAll(PDO::FETCH_ASSOC);

    // Create array: room name => price_per_night
    $roomPrices = [];
    foreach ($allRooms as $room) {
        $roomPrices[$room['id']] = (int)$room['price_per_night'];
    };

    // Count total price for choosen room:
    $totalRoomCost = 0;

    if (isset($selectedRoomId, $roomPrices[$selectedRoomId])) {
        $pricePerNight = $roomPrices[$selectedRoomId];
        $totalRoomCost = $pricePerNight * $nights;
    };
};

// ------------------------------------------- BOOK FEATURE ---------------------------------------------
if (isset($_POST['features'], $_POST['arrivalDate'])) {
    // ------------------------------------------- PAYMENT FEATURE ---------------------------------------------
    // Fetch selected features from form
    $selectedFeatures = $_POST['features'] ?? [];
    $totalFeatureCost = 0;

    // Fetch price for each feature in DB:
    // $pdoAllFeatures = $pdo->prepare("SELECT features.id, tiers.price_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id");
    $pdoAllFeatures = $pdo->prepare("SELECT features.id, tiers.cost_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id");
    $pdoAllFeatures->execute();
    $allFeatures = $pdoAllFeatures->fetchAll(PDO::FETCH_ASSOC);

    // Create array: Feature id => price - this works like a pricelist
    $featurePrices = [];
    foreach ($allFeatures as $feature) {
        // $featurePrices[$feature['id']] = (int)$feature['price_per_feature'];
        $featurePrices[$feature['id']] = (int)$feature['cost_per_feature'];
    };

    // Takes each selected Feature, find the price in price list, and sums it up in totalFeatureCost
    foreach ($selectedFeatures as $featureId) {
        if (isset($featurePrices[$featureId])) {
            $totalFeatureCost += $featurePrices[$featureId];
        };
    };
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
$guestDB = $pdo->prepare("SELECT id FROM guests WHERE name = :name");
$guestDB->bindParam(":name", $name, PDO::PARAM_STR);
$guestDB->execute();
$guestDB = $guestDB->fetch(PDO::FETCH_ASSOC);

// Register guest in DB if doesn't exists
if (!$guestDB) {
    $registerGuest = $pdo->prepare("INSERT INTO guests (name) VALUES (:name)");
    $registerGuest->bindParam(":name", $name, PDO::PARAM_STR);
    $registerGuest->execute();

    $guestDB = $pdo->prepare("SELECT id FROM guests WHERE name = :name");
    $guestDB->bindParam(":name", $name, PDO::PARAM_STR);
    $guestDB->execute();
    $guestDB = $guestDB->fetch(PDO::FETCH_ASSOC);
};

// Fetch its id (used in checkin to register bookings)
$guestId = $guestDB['id'];


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

    $registerBookedRoom = $pdo->prepare("INSERT INTO checkins (guest_id, room_id, arrival, departure) VALUES (:guest_id, :room_id, :arrival, :departure)");
    $registerBookedRoom->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $registerBookedRoom->bindParam(":room_id", $selectedRoomId, PDO::PARAM_INT);
    $registerBookedRoom->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $registerBookedRoom->bindParam(":departure", $departureDT->format('Y-m-d H:i'), PDO::PARAM_STR);

    $registerBookedRoom->execute();
}
// ------------------------------------ REGISTER FEATURE-ONLY-CUSTOMERS --------------------------------------
// Requires: guest_id, arrival --> customer gets checkin_id (used for registering features)

if (!isset($selectedRoomId) && isset($arrivalDT)) {

    // Check that at least 1 feature is choosen
    if (empty($selectedFeatures)) {
        $errors[] = "You must select at least one feature!";
        header("Location: /../index.php");
        //Stop script
        exit;
    }

    // if (!isset($selectedRoomId) && isset($arrivalDT, $selectedFeatures)) {

    $registerFeatureOnly = $pdo->prepare("INSERT INTO checkins (guest_id, arrival) VALUES (:guest_id, :arrival)");
    $registerFeatureOnly->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $registerFeatureOnly->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);

    $registerFeatureOnly->execute();
}


// -------------------------------------- REGISTER BOOKED FEATURE IN DB ----------------------------------------
//Requires: checkin_id, choosen feature_id
if (!empty($selectedFeatures)) {
    //Fetch checkin_id
    $checkinId = $pdo->prepare("SELECT id FROM checkins WHERE guest_id = :guest_id AND arrival = :arrival ORDER BY id DESC LIMIT 1");
    $checkinId->bindParam(":guest_id", $guestId, PDO::PARAM_STR);
    $checkinId->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $checkinId->execute();
    $checkinId = $checkinId->fetch(PDO::FETCH_ASSOC);
    $checkinId = $checkinId['id'];

    // Loop through list of choosen features, each feature gets an own row in DB
    foreach ($selectedFeatures as $featureId) {
        $registerBookedFeature = $pdo->prepare("INSERT INTO checkin_feature (checkin_id, feature_id) VALUES (:checkin_id, :feature_id)");
        $registerBookedFeature->bindParam(":checkin_id", $checkinId, PDO::PARAM_INT);
        $registerBookedFeature->bindParam(":feature_id", $featureId, PDO::PARAM_INT);
        $registerBookedFeature->execute();
    };
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
