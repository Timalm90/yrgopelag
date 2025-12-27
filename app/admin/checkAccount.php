<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";
require __DIR__ . "/../config.php";

use GuzzleHttp\Exception\RequestException;

$adminErrors = [];

if (!isset($_POST['username'], $_POST['apiKey']) || $_POST['username'] === '' || $_POST['apiKey'] === '') {
    $adminErrors[] = "Username and API-key are mandatory";
    header("Location: ../../view/admin.php");
    exit;
}

$user = trim($_POST['username']);
$apiKey = trim($_POST['apiKey']);

try {
    $accountResponse = $client->post('/centralbank/accountInfo', [
        'json' => [
            'user' => $user,
            'api_key' => $apiKey
        ]
    ]);

    $accountResult = json_decode($accountResponse->getBody()->getContents(), true);

    if (!isset($accountResult['credit'])) {
        $adminErrors[] = getErrorMessage($accountResult['error'] ?? '');
    }
} catch (RequestException $accountException) {
    if ($accountException->hasResponse()) {
        $apiError = json_decode($accountException->getResponse()->getBody()->getContents(), true)['error'] ?? '';
        $adminErrors[] = getErrorMessage($apiError);
    } else {
        $adminErrors[] = "Could not reach the Centralbank. Please try again later.";
    }
}

if (!empty($adminErrors)) {
    $_SESSION['adminErrors'] = $adminErrors;
    header("Location: ../../view/admin.php");
    exit;
}

$_SESSION['accountInfo'] = [
    'user' => $user,
    'credit' => (int) $accountResult['credit']
];

header("Location: ../../view/admin.php");
exit;
