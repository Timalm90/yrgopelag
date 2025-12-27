<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";
require __DIR__ . "/../config.php";

use GuzzleHttp\Exception\RequestException;

$addFeatureErrors = [];

// Validate input
if (!isset($_POST['item']) || empty($_POST['item'])) {
    $addFeatureErrors[] = "No feature selected";
    $_SESSION['adminError'] = $addFeatureErrors;
    header("Location: ../../view/admin.php");
    exit;
}

// Find selected non-active feature
$featureId = (int) $_POST['item'];
$allFeatures = findNonActiveFeatures($pdoBooking);
$newFeature = null;
foreach ($allFeatures as $feature) {
    if ((int)$feature['id'] === $featureId) {
        $newFeature = $feature;
        break;
    }
}

if (!$newFeature) {
    $addFeatureErrors[] = "Invalid selection";
    $_SESSION['adminError'] = $addFeatureErrors;
    header("Location: ../../view/admin.php");
    exit;
}

$featureCost = (int) $newFeature['cost_per_tier'];
$user = 'Emilie';  // Hotel owner
$transferCode = NULL;

// Register bought feature in Centralbank
try {
    $featuresToRegister = [
        $newFeature['activity'] => [
            $newFeature['tier'] => $newFeature['feature']
        ]
    ];

    $registerResponse = $client->post('/centralbank/islands', [
        'json' => [
            'islandName' => "Starlight Island",
            'hotelName' => "Yoshi's Resort",
            'url' => "https://developedbyemilie/yrgopelag",
            'stars' => 5,
            'user' => $user,
            'api_key' => $apiKey,
            'features' => $featuresToRegister
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
        $addFeatureErrors[] = "Could not register feature at Centralbank.";
    }
}

//Error handling if anything goes wrong in connection to API
if (!empty($addFeatureErrors)) {
    $_SESSION['adminError'] = $addFeatureErrors;
    header("Location: ../../view/admin.php");
    exit;
}

// Set feature to active in database
$stmt = $pdoBooking->prepare("UPDATE features SET is_active = 1 WHERE id = :id");
$stmt->bindParam(":id", $featureId, PDO::PARAM_INT);
$stmt->execute();

$adminSuccess = "Feature added successfully";
$_SESSION['addFeature'] = $adminSuccess;
header("Location: ../../view/admin.php");
exit;
