<span class="weekday">Mo</span>
<span class="weekday">Tu</span>
<span class="weekday">We</span>
<span class="weekday">Th</span>
<span class="weekday">Fr</span>
<span class="weekday">Sa</span>
<span class="weekday">Su</span>

<div class="day emptyDay"></div>
<div class="day emptyDay"></div>
<div class="day emptyDay"></div>

<?php
for ($i = 1; $i <= 31; $i++) : ?>
    <div class="day
            <?php
            if ($i % 7 === 4 || $i === 1 || $i === 6) {
                // In januari 2026, 4, 11, 18 & 25 are sundays. 1st & 6th is red day
                echo " weekend";
            };

            foreach ($booked as $date) {
                if ($i === $date) {
                    echo " booked";
                }
            } ?>"

        data-room="<?= $room['id'] ?>"
        data-date="2026-01-<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>">

        <?= $i; ?>
    </div>
<?php endfor; ?>
<div class="day emptyDay"></div>