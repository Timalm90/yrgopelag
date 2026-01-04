<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

// Fetch data from form
$category = $_POST['category'] ?? null;
$item = $_POST['item'] ?? null;
$price = $_POST['price'] ?? null;
$adminErrors = [];

// Check if mandatory data is given
if (!isset($category, $item, $price)) {
    $adminErrors[] = "Change price: Category, item and price required";
};

// Trim whitespace, check if price is an integer
$category = trim($category);
$item = trim($item);
$price = filter_var($price, FILTER_VALIDATE_INT);

if ($price === FALSE  || $price <= 0) {
    $adminErrors[] = "Change price: Price is not valid (must be a positive integer)";
};

// If error, don't update database, redirect and exit script
if (!empty($adminErrors)) {
    $_SESSION['adminErrors'] = $adminErrors;
    header("Location: ../../view/admin.php");
    exit;
};

// Update price in database
switch ($category) {
    case 'room':
        updateRoomPrice($pdoBooking, $item, $price);
        break;

    case 'tier':
        updateTierPrice($pdoBooking, $item, $price);
        break;

    default:
        $adminErrors[] = "Change price: Could not update price. Try again later";
}

if (!empty($adminErrors)) {
    $_SESSION['adminErrors'] = $adminErrors;
    header("Location: ../../view/admin.php");
    exit;
};


$item = ucwords($item);

$adminSuccess = "Change price: Price for the $category $item was updated successfully";
$_SESSION['adminSuccess'] = $adminSuccess;
header("Location: ../../view/admin.php");
exit;
