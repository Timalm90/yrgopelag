<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

$username = $_POST['username'];
$username = trim($username);

$password = $_POST['password'];

if ($username === '' || $password === '') {
    handleLoginError();
};

// Fetch admin in database
$dbAdmin = findAdmin($pdoBooking, $username);

// If not founbd, redirect back to login page.
if (!$dbAdmin) {
    handleLoginError();
}

$dbUser = $dbAdmin['username'];
$dbPassword = $dbAdmin['password'];

// Verify password
$verified = password_verify($password, $dbPassword);

// If valid, store in session varible, else redirect to login page
if ($verified) {
    $_SESSION['admin'] = [
        "name" => $dbUser,
    ];
} else {
    handleLoginError();
};

// Redirect to admin page
header("Location: ../../view/admin.php");
exit;
