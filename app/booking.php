<?php

declare(strict_types=1);

// -------------------------------------------------------------------------------------------------------------
//JUST FOR TESTING
require __DIR__ . "/autoload.php";
//JUST FOR TESTING
// -------------------------------------------------------------------------------------------------------------
// ------------------------------------------- ERROR HANDLING ---------------------------------------------
$errors = [];
// --------------------------------------------------------------------------------------------------------

// Check if mandatory information is provided (name & transferCode)
if (!isset($_POST['name'], $_POST['transferCode']) || $name === '' || $transferCode === '') {
    $errors[] = "Name and transferCode is mandatory!";
    header("Location: index.php");
}

// Sanitize & validate inputs, prevent XSS
$name = $_POST['name'];
$name = htmlspecialchars(trim($name));

$transferCode = $_POST['transferCode'];
$transferCode = htmlspecialchars($transferCode);
// -------------------------------------------------------------------------------------------------------------

// HOTELL ROOM
if (isset($_POST['room'], $_POST['arrivalDate'], $_POST['departureDate'])) {
    //Fetch input from form:
    $selectedRoomId = $_POST['room'];
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];

    // Omvandla till DateTime and append checkin/checkout times
    $arrivalDT = new DateTime($arrivalDate . ' 15:00');
    $departureDT = new DateTime($departureDate . ' 11:00');

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
        $errors[] = "Room is not avaiable on choosen dates.";
    }
};

// ---------------------------------------- PAYMENT HOTELL ROOM ------------------------------------------

//Count number of nights
$nights = 0;

$arrivalDay   = (int)$arrivalDT->format('j');   // -> Day as int
$departureDay = (int)$departureDT->format('j'); // -> Day as int

if ($departureDay < $arrivalDay) {
    $nights = 0; //Unneseccary?
    $errors[] = "Check your dates for arrival and departure.";
    header("Location: index.php");
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

if ($selectedRoomId && isset($roomPrices[$selectedRoomId])) {
    $pricePerNight = $roomPrices[$selectedRoomId];
    $totalRoomCost = $pricePerNight * $nights;
};

// ------------------------------------------- PAYMENT FEATURE ---------------------------------------------
// Fetch selected features from form
$selectedFeatures = $_POST['features'];
$totalFeatureCost = 0;

// Fetch cost for each feature in DB:
$pdoAllFeatures = $pdo->prepare("SELECT features.id, tiers.cost_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id");
$pdoAllFeatures->execute();
$allFeatures = $pdoAllFeatures->fetchAll(PDO::FETCH_ASSOC);

// Create array: Feature id => cost_per_feature - this works like a pricelist
$featurePrices = [];
foreach ($allFeatures as $feature) {
    $featurePrices[$feature['id']] = (int)$feature['cost_per_feature'];
};

// Takes each selected Feature, find the price in price list, and sums it up in totalFeatureCost
foreach ($selectedFeatures as $featureId) {
    if (isset($featurePrices[$featureId])) {
        $totalFeatureCost += $featurePrices[$featureId];
    }
};

// ------------------------------------------- TOTAL PRICE ---------------------------------------------

$totalPrice = $totalRoomCost + $totalFeatureCost;

// ----------------------------------------------------------------------------------------------------

// Create a receipt and send to centralbank
// Make insert into in database!
// Make a request to centralbank to make a deposit
// Show confirmation message to user!


// ------------------------------------------- TESTING SECTION ---------------------------------------------
// Test for payment for rooms - seems to work fine!
echo "<pre>";
echo $name . "<br>";
echo $transferCode . "<br>";
echo "Selected room ID: $selectedRoomId <br>";
var_dump($arrivalDate);
echo "<br>";
var_dump($departureDate);
echo "<br>";
echo "Nights: $nights <br>";
echo "Total price for room: $totalRoomCost credits <br>";
echo "Total price for features: $totalFeatureCost credits <br>";

var_dump($_POST['arrivalDate']) . "<br>";
var_dump($_POST['departureDate']) . "<br>";



// var_dump($roomPrices); -->
// [
//   "budget"   => 2,
//   "standard" => 5,
//   "luxury"   => 10
// ]

// var_dump($featurePrices);
// array(16) {
//   [1]=>
//   int(1)
//   [2]=>
//   int(3)
//   [3]=>
//   int(6)
//   [4]=>
//   int(10)
//   [5]=>
//   int(1)
//   [6]=>
//   int(3)
//   [7]=>
//   int(6)
//   [8]=>
//   int(10)
//   [9]=>
//   int(1)
//   [10]=>
//   int(3)
//   [11]=>
//   int(6)
//   [12]=>
//   int(10)
//   [13]=>
//   int(1)
//   [14]=>
//   int(3)
//   [15]=>
//   int(6)
//   [16]=>
//   int(10)
// }
