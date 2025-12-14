<?php

declare(strict_types=1);

// This is a calendar file copied from lesson.
// TO DO:
// Numbers in booked should be fetched from database
// Calendar should be required into index/booking-site x3. 
// Each calendar should be unique for each room.



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

<?php
    return $calendar;
}

// ------------------------------------- LOG -------------------------------------
// 2025-12-13: Start up. Removed unnecessary code lines. Added 3 empty days and red days for January 2026. Change modulo to 4 -> 4th = 1st Sunday.