<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

// In this file we login admins.

$username = $_POST['username'];
$username = trim($username);

$password = $_POST['password'];

if ($username === '' || $password === '') {
    handleLoginError();
};

// Fetch admin in database
$dbAdmin = findAdmin($pdoBooking, $username);

// If admin wasn't found in the database, redirect the user back to the login page.
if (!$dbAdmin) {
    handleLoginError();
}

$dbUser = $dbAdmin['username'];
$dbPassword = $dbAdmin['password'];

// If admin was found in database, verify the password against the one in the database.
$verified = password_verify($password, $dbPassword);

// If password was valid, store the admin's username in a session variable called user.
if ($verified) {
    $_SESSION['admin'] = [
        "name" => $dbUser,
    ];
} else {
    handleLoginError();
    // header("Location: ../../view/login.php");
    // exit;
};

header("Location: ../../view/admin.php");
exit;
