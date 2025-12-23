<?php

declare(strict_types=1);
// require __DIR__ . "/autoload.php"; // Access to database, functions, DB-paths. Commented out, this lead to double sessions?

// Get all rooms
$roomPresentation = $pdoBooking->prepare("SELECT * FROM rooms");
$roomPresentation->execute();
$rooms = $roomPresentation->fetchAll(PDO::FETCH_ASSOC);

// Cheatsheet:
// $roomPresentation['id'] = 1/2/3
// $roomPresentation['room'] = budget/standard/luxuary
// $roomPresentation['price_per_night'] = int/int/int
// $roomPresentation['title'] = str/str/str
// $roomPresentation['description'] = str/str/str


// ------------------------------ Fetch occupied dates ------------------------------
// Array for all booked dates
$bookedByRoom = [];

// Fetch all checkins for the specific room
foreach ($rooms as $room) {
    $booked = $pdoBooking->prepare("SELECT arrival, departure FROM bookings WHERE room_id = :id");
    $booked->bindParam(':id', $room['id'], PDO::PARAM_INT);
    $booked->execute();
    $bookedByRoom[$room['id']] = $booked->fetchAll(PDO::FETCH_ASSOC);
}

// ['arrival' => '2026-01-01 15:00', 'departure' => '2026-01-03 11:00'] --> [1, 2]

function bookedDays(array $bookings): array
{
    $days = [];

    foreach ($bookings as $b) {
        // DB returns string, convert to DateTime 
        $startDT = new DateTime($b['arrival']);
        $endDT   = new DateTime($b['departure']);

        // Extract day, convert to int
        $startDay = (int)$startDT->format('j');
        $endDay   = (int)$endDT->format('j');

        // Loop through all occupied dates, exclude checkout day
        for ($i = $startDay; $i < $endDay; $i++) {
            $days[] = $i;
        }
    }

    return $days;
}
