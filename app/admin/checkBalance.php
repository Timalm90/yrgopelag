<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";
require __DIR__ . "/../config.php"; // -> $apiKey

use GuzzleHttp\Exception\RequestException;

header('Content-Type: application/json');

$hotelOwner = getSettingsValue($pdoBooking, "hotel_owner");

try {
    $accountResponse = $client->post('/centralbank/accountInfo', [
        'json' => [
            'user' => $hotelOwner,
            'api_key' => $apiKey
        ]
    ]);

    $accountResult = json_decode($accountResponse->getBody()->getContents(), true);

    if (!isset($accountResult['credit'])) {
        echo json_encode(['error' => 'No credit info returned']);
        exit;
    }

    echo json_encode(['credit' => (int)$accountResult['credit']]);
} catch (RequestException $e) {
    echo json_encode(['error' => 'Could not reach centralbank']);
}
