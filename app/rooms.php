<?php

declare(strict_types=1);

// Fetch occupied dates
$bookedByRoom = [];

foreach ($rooms as $room) {
    $bookedByRoom[$room['id']] = checkAvailable(
        $pdoBooking,
        (int)$room['id']
    );
}
