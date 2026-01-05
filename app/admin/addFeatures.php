<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";
require __DIR__ . "/../config.php";

use GuzzleHttp\Exception\RequestException;

// Start values
// $islandName = getSettingsValue($pdoBooking, "island_name"); //
// $hotelName = getSettingsValue($pdoBooking, "hotel_name"); //
// $starRating = getSettingsValue($pdoBooking, "star_rating"); //
$starRating = (int)$starRating;
// $hotelOwner = getSettingsValue($pdoBooking, "hotel_owner"); //
// $url = getSettingsValue($pdoBooking, "webpage"); //
$addFeatureErrors = [];

// Validate input
if (!isset($_POST['item']) || empty($_POST['item'])) {
    $addFeatureErrors[] = "Add feature: No feature selected";

    handleAdminErrors($addFeatureErrors);
    // $_SESSION['adminErrors'] = $addFeatureErrors;
    // header("Location: ../../view/admin.php");
    // exit;
}

// Find selected non-active feature
$featureId = (int) $_POST['item'];
// $allFeatures = findNonActiveFeatures($pdoBooking);
$newFeature = null;
foreach ($allFeatures as $feature) {
    if ((int)$feature['id'] === $featureId) {
        $newFeature = $feature;

        // To match request to centralbank/island
        $newFeature['activity'] = $newFeature['category'];
        break;
    }
}

if (!$newFeature) {
    $addFeatureErrors[] = "Add feature: Invalid selection";

    handleAdminErrors($addFeatureErrors);
    // $_SESSION['adminErrors'] = $addFeatureErrors;
    // header("Location: ../../view/admin.php");
    // exit;
}

// Fetch active features from Centralbank
try {
    $activeResponse = $client->post('/centralbank/islandFeatures', [
        'json' => [
            'user' => $hotelOwner,
            'api_key' => $apiKey
        ]
    ]);

    $activeResult = json_decode($activeResponse->getBody()->getContents(), true);

    $activeFeatures = $activeResult['features'] ?? [];
} catch (RequestException $activeException) {
    $addFeatureErrors[] = "Add feature: Could not fetch active features";

    handleAdminErrors($addFeatureErrors);
    // if (!empty($addFeatureErrors)) {
    //     $_SESSION['adminErrors'] = $addFeatureErrors;
    //     header("Location: ../../view/admin.php");
    //     exit;
    // }
};

// Convert active features to right format for "/centralbank/islands"
$featureToRegister = [];
foreach ($activeFeatures as $feature) {
    $featureToRegister[$feature['activity']][$feature['tier']] = $feature['feature'];
}

// Register bought feature in Centralbank
try {
    $featureToRegister[$newFeature['activity']][$newFeature['tier']] = $newFeature['feature'];

    // islandName, hotelName, url, stars, user, api_key, hotel_specific_name (optional), features[activity][tier]=name...
    $registerResponse = $client->post('/centralbank/islands', [
        'json' => [
            'islandName' => $islandName,
            'hotelName' => $hotelName,
            'url' => $url,
            'stars' => $starRating,
            'user' => $hotelOwner,
            'api_key' => $apiKey,
            'hotel_specific_name' => 'mario-themed',
            'features' => $featureToRegister
        ]
    ]);

    $registerResult = json_decode($registerResponse->getBody()->getContents(), true);

    if (isset($registerResult['error'])) {
        $addFeatureErrors[] = $registerResult['error'];
    }
} catch (RequestException $registerException) {
    if ($registerException->hasResponse()) {
        $apiError = json_decode($registerException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        $addFeatureErrors[] = getErrorMessage($apiError);
    } else {
        $addFeatureErrors[] = "Add feature: Could not register feature at Centralbank.";
    }
}

//Error handling if anything goes wrong in connection to API
handleAdminErrors($addFeatureErrors);
// if (!empty($addFeatureErrors)) {
//     $_SESSION['adminErrors'] = $addFeatureErrors;
//     header("Location: ../../view/admin.php");
//     exit;
// }

// Set feature to active in database
// $stmt = $pdoBooking->prepare("UPDATE features SET is_active = 1 WHERE id = :id");
// $stmt->bindParam(":id", $featureId, PDO::PARAM_INT);
// $stmt->execute();

activateFeature($pdoBooking, $featureId);

// $item = $feature['activity'];
$item = ucwords($newFeature['feature']);
$adminSuccess = "Add feature: $item was added successfully";
$_SESSION['adminSuccess'] = $adminSuccess;
header("Location: ../../view/admin.php");
exit;
