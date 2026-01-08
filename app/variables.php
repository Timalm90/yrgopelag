<?php

declare(strict_types=1);

// Island & Hotel info
$islandName = getSettingsValue($pdo, "island_name"); // Starlight Island
$hotelName = getSettingsValue($pdo, "hotel_name"); // Yoshi's Resort
$starRating = getSettingsValue($pdo, "star_rating"); // "5"
$hotelOwner = getSettingsValue($pdo, "hotel_owner"); // Emilie
$url = getSettingsValue($pdo, "webpage"); // https://developedbyemilie.se/yrgopelag

// Rooms & Features
$rooms = getRooms($pdo);
$tiers = getTierLevels($pdo);
$allFeatures = findNonActiveFeatures($pdo);
$topFeatures = topFeatures($pdo);
$daypassCount = countDayPass($pdo);
$bookedRoomCount = countRoomBookings($pdo);
$activeFeatures = getActiveFeatures($pdo);
$showOffers = getDiscountInfo($pdo);
$featurePrice = getFeaturePrices($pdo);
$categories = getCategories($pdo);

// Discounts in booking.php
$luxuryRoomId = getLuxuryRoomId($pdo); //int in FE
$bowserFeatureId = getBowserFeatureId($pdo); //int in FE
$loyalDiscount = getDiscount($pdo, 'loyal'); //int
$comboDiscount = getDiscount($pdo, 'luxuryCombo'); //int

// Checkin/out times
$checkinTime = "15:00";
$checkoutTime = "11:00";
