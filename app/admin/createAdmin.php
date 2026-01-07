<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";
require __DIR__ . "/../config.php";

header("Content-Type: application/json");

// Fetch JSON-data
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['newUsername'] ?? null;
$password1 = $data['password1'] ?? null;
$password2 = $data['password2'] ?? null;
$mastercodeInput = $data['masterCode'] ?? null;

$errors = [];

// -------- VALIDATION --------
if (!$username || !$password1 || !$password2) {
    $errors[] = "Create admin: Username and passwords are required";
}

$username = trim((string)$username);

// Username length
if (strlen($username) < 3 || strlen($username) > 20) {
    $errors[] = "Create admin: Username must be 3-20 characters";
}

// Username format
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = "Create admin: Username may only contain letters, numbers and underscore";
}

// Passwords match
if ($password1 !== $password2) {
    $errors[] = "Create admin: Passwords are not identical";
}

// Password rules
if (strlen((string)$password1) < 8) {
    $errors[] = "Create admin: Password must be at least 8 characters";
}

if (!preg_match('/[0-9]/', (string)$password1)) {
    $errors[] = "Create admin: Password must contain at least one number";
}

if (!preg_match('/[A-Z]/', (string)$password1)) {
    $errors[] = "Create admin: Password must contain at least one uppercase letter";
}

// If validation fails
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'error' => implode('. ', $errors)
    ]);
    exit;
}

// Check mastercode
$masterCodeInput = $data['masterCode'] ?? null;
requireMastercode($masterCodeInput, $mastercode);

// -------- DATABASE --------
$hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

try {
    $statement = $pdoBooking->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
    $statement->bindParam(":username", $username, PDO::PARAM_STR);
    $statement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
    $statement->execute();

    echo json_encode([
        'success' => true,
        'message' => "Create admin: Admin '$username' was created successfully"
    ]);
    exit;
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => "Create admin: Could not create admin"
    ]);
    exit;
}
