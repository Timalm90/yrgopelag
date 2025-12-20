<div class="day"></div>
<div class="day"></div>
<div class="day"></div>

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
            } ?>">
        <?= $i; ?>
    </div>
<?php endfor; ?>
<div class="day"></div>