<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";
// In this file we login admins.

// Fetch username and password from form
if (isset($_POST['username'], $_POST['password'])) {
    // Fetch and trim the input values and store it in variables
    $username = $_POST['username'];
    $username = trim($username);

    $password = $_POST['password'];

    // Fetch admin in database
    $statement = $pdoBooking->prepare("SELECT * FROM admins WHERE username = :username AND is_active = 1");
    $statement->bindParam(":username", $username, PDO::PARAM_STR);
    $statement->execute();
    $dbRow = $statement->fetch(PDO::FETCH_ASSOC);
    $dbUser = $dbRow['username'];
    $dbPassword = $dbRow['password'];

    // If admin wasn't found in the database, redirect the user back to the login page.
    if (!$dbUser) {
        header("Location: ../../view/login.php");
        exit;
    };

    // If admin was found in database, verify the password against the one in the database.
    $verified = password_verify($password, $dbPassword);

    // If password was valid, store the admin's username in a session variable called user.
    if ($verified) {
        $_SESSION['admin'] = [
            "name" => $dbUser,
        ];
    } else {
        header("Location: ../../view/login.php");
        exit;
    };
};

header("Location: ../../view/admin.php");
