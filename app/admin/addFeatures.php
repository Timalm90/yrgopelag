<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";

use GrahamCampbell\ResultType\Success;
use GuzzleHttp\Exception\RequestException;

header("Content-Type: application/json");

// Start values
$starRating = (int)$starRating;

$data = json_decode(file_get_contents("php://input"), true);
$featureId = $data['item'] ?? null;
$mastercodeInput = $data['masterCode'] ?? null;
$errors = [];

// Validate input
if (!$featureId) {
    echo json_encode([
        'success' => false,
        'error' => "Add feature: No feature selected"
    ]);
    exit;
}

// Find selected non-active feature
$featureId = (int)$featureId;
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
    echo json_encode([
        'success' => false,
        'error' => "Add feature: Invalid selection"
    ]);
    exit;
}

//Check mastercode
requireMastercode($mastercodeInput, $mastercode);

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
    echo json_encode([
        'success' => false,
        'error' => "Add feature: Could not fetch active features"
    ]);
    exit;
};

// Convert active features to API-format
$featureToRegister = [];
foreach ($activeFeatures as $feature) {
    $featureToRegister[$feature['activity']][$feature['tier']] = $feature['feature'];
}

// Register bought feature in Centralbank
try {
    $featureToRegister[$newFeature['activity']][$newFeature['tier']] = $newFeature['feature'];

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
        echo json_encode([
            'success' => false,
            'error' => $registerResult['error']
        ]);
        exit;
    }
} catch (RequestException $registerException) {
    if ($registerException->hasResponse()) {
        $apiError = json_decode($registerException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        echo json_encode([
            'success' => false,
            'error' => getErrorMessage($apiError)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => "Add feature: Could not register feature at Centralbank, please try again later."
        ]);
    }
    exit;
}

activateFeature($pdoBooking, $featureId);

$item = ucwords($newFeature['feature']);
$adminSuccess = "Add feature: $item was added successfully";

echo json_encode([
    'success' => true,
    'message' => $adminSuccess
]);
exit;
