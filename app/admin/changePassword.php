<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

header("Content-Type: application/json");

// Fetch JSON-data
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['usernameChangePassword'] ?? null;
$currentPassword = $data['currentPassword'] ?? null;
$changePassword1 = $data['changePassword1'] ?? null;
$changePassword2 = $data['changePassword2'] ?? null;
$mastercodeInput = $data['masterCode'] ?? null;

$errors = [];

// -------- VALIDATION --------
if (!$username || !$currentPassword || !$changePassword1 || !$changePassword2) {
    $errors[] = "Change password: All fields are required";
}

$username = trim((string)$username);

// Find user in DB
$admin = findAdmin($pdoBooking, $username);

if (!$admin) {
    $errors[] = "Change password: Admin not found";
}

if ($admin && password_verify($currentPassword, $admin['password'])) {

    if ($changePassword1 !== $changePassword2) {
        $errors[] = "Change password: Passwords are not identical";
    }

    if (strlen($changePassword1) < 8) {
        $errors[] = "Change password: Password must be at least 8 characters";
    }

    if (!preg_match('/[0-9]/', $changePassword1)) {
        $errors[] = "Change password: Password must contain at least one number";
    }

    if (!preg_match('/[A-Z]/', $changePassword1)) {
        $errors[] = "Change password: Password must contain at least one uppercase letter";
    }
} else {
    $errors[] = "Change password: Current password is incorrect";
};

// If validation fails
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'error' => implode('. ', $errors)
    ]);
    exit;
}

// Check mastercode
requireMastercode($mastercodeInput, $mastercode);

// -------- DATABASE --------
$hashedPassword = password_hash($changePassword1, PASSWORD_DEFAULT);

try {
    $statement = $pdoBooking->prepare("UPDATE admins SET password = :password WHERE username = :username");
    $statement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
    $statement->bindParam(":username", $username, PDO::PARAM_STR);
    $statement->execute();

    echo json_encode([
        'success' => true,
        'message' => "Change password: Password for  '$username' was changed successfully"
    ]);
    exit;
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => "Change password: Could not change password"
    ]);
    exit;
}
