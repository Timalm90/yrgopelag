<?php

declare(strict_types=1);

require __DIR__ . '/autoload.php';
require __DIR__ . '/config.php';

use GuzzleHttp\Exception\RequestException;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['name'], $input['apiKey'], $input['amount'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$name = trim($input['name']);
$apiKey = trim($input['apiKey']);
$amount = (int)$input['amount'];

try {
    $response = $client->post('/centralbank/withdraw', [
        'json' => [
            'user' => $name,
            'api_key' => $apiKey,
            'amount' => $amount
        ]
    ]);

    $result = json_decode($response->getBody()->getContents(), true);

    if (!isset($result['transferCode'])) {
        echo json_encode(['error' => 'Could not generate transfer code']);
        exit;
    }

    echo json_encode(['transferCode' => $result['transferCode']]);
} catch (RequestException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
