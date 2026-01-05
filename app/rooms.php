<?php

declare(strict_types=1);

// Get all rooms
// $rooms = getRooms($pdoBooking);

// ------------------------------ Fetch occupied dates ------------------------------
// Array for all booked dates
$bookedByRoom = [];

foreach ($rooms as $room) {
    $bookedByRoom[$room['id']] = checkAvailable(
        $pdoBooking,
        (int)$room['id']
    );
}
