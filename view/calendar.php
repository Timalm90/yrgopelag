<?php

declare(strict_types=1);

// This is a calendar file copied from lesson.

// -------------------------------- TO DO --------------------------------

// VIEW (view/calendar.php):
// - Remove the function entirely.
// - Let this file be pure markup + light PHP (loops/conditions).
// - The HTML structure here should match exactly what is rendered.
// - Calendar view should be reusable and required into
//   index.php / booking pages (x3 calendars, one per room).


// LOGIC (app/calendarLogic.php):
// - Fetch booked dates from database (arrival/departure).
// - Convert bookings to an array of occupied day numbers.
// - Support multiple rooms (calendar must be unique per room).
// - Keep all calculations (weekends, booked days, month logic)
//   OUT of the view.


// CONTROLLER (index.php / booking page):
// - Require calendarLogic.php at the top.
// - Call calendar logic once per room. (Each calendar should be unique for each room)
// - Pass resulting data to the calendar view.
// --------------------------------------------------------------------------



// Days when the room is booked
$booked = [2, 8, 19, 27, 28];

function myCalendar(array $array): string
{
    $calendar = "<section class=\"calendar\">";
?>

    <div class="day"></div>
    <div class="day"></div>
    <div class="day"></div>

    <?php
    for ($i = 1; $i <= 31; $i++) :
    ?>
        <div class="day
            <?php
            if ($i % 7 === 4 || $i === 1 || $i === 6) {
                // In januari 2026, 4, 11, 18 & 25 are sundays. 1st & 6th is red day
                echo " weekend";
            };

            foreach ($array as $bookedDate) {
                if ($i === $bookedDate) {
                    echo " booked";
                }
            } ?>">
            <?= $i; ?>
        </div>
    <?php endfor; ?>
    <div class="day"></div>
<?php
    return $calendar;
}

// ------------------------------------- LOG -------------------------------------
// 2025-12-13: Start up. Removed unnecessary code lines. Added 3 empty days and red days for January 2026. Change modulo to 4 -> 4th = 1st Sunday.