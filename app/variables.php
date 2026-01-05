<?php

declare(strict_types=1);

$islandName = getSettingsValue($pdoBooking, "island_name"); // Starlight Island

$hotelName = getSettingsValue($pdoBooking, "hotel_name"); // Yoshi's Resort

$starRating = getSettingsValue($pdoBooking, "star_rating"); // "5"

$hotelOwner = getSettingsValue($pdoBooking, "hotel_owner"); // Emilie

$url = getSettingsValue($pdoBooking, "webpage"); // https://developedbyemilie.se/yrgopelag

$rooms = getRooms($pdoBooking);
$tiers = getTierLevels($pdoBooking);
$allFeatures = findNonActiveFeatures($pdoBooking);


// // DISCOUNT VARIABLES in booking.php
// $luxuryRoomId = getLuxuryRoomId($pdoBooking); //int in FE

// $bowserFeatureId = getBowserFeatureId($pdoBooking); //int in FE

// $loyalDiscount = getDiscount($pdoBooking, 'loyal');
// $comboDiscount = getDiscount($pdoBooking, 'luxuryCombo');
// //getDiscount returnerar int
