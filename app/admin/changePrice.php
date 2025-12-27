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
    $adminErrors[] = "Category, item and price required";
};

// Trim whitespace, check if price is an integer
$category = trim($category);
$item = trim($item);
$price = filter_var($price, FILTER_VALIDATE_INT);

if ($price === FALSE  || $price <= 0) {
    $adminErrors[] = "Price is not valid (must be a positive integer)";
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

    case 'featureTier':
        updateTierPrice($pdoBooking, $item, $price);
        break;

    default:
        $adminErrors[] = "Could not update price. Try again later";
}

if (!empty($adminErrors)) {
    $_SESSION['adminErrors'] = $adminErrors;
    header("Location: ../../view/admin.php");
    exit;
};

$adminSuccess = "Price updated successfully";
$_SESSION['updatePrice'] = $adminSuccess;
header("Location: ../../view/admin.php");
exit;
