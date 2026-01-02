    <div class="roomWrapper">
        <?php foreach ($rooms as $room):
            $booked = bookedDays($bookedByRoom[$room['id']]);
        ?>
            <section class="roomSection whiteBox">
                <article class="roomImg">
                    <img class="roomImg" src="assets/images/<?= htmlspecialchars($room['room']) ?>.png" alt="Picture of <?= htmlspecialchars($room['room']) ?> room" />
                </article>
                <article class="roomDescription">
                    <h2><?= htmlspecialchars($room['title']) ?></h2>
                    <p><?= htmlspecialchars($room['description']) ?></p>
                </article>
                <article class="roomCalendar">
                    <p>Jan 2026</p>
                    <div class="calendar">
                        <?php
                        require __DIR__ . '/calendar.php'; ?>
                    </div>
                    <div class="calendarGuide">
                        <div class="calendarDot"></div> <span> = occupied</span>
                    </div>
                </article>
            </section>
        <?php endforeach; ?>

        <img class="roomCharacter hiddenCharacter" src="assets/images/toad.png" alt="Toad icon" />
        <img class="roomCharacter" src="assets/images/luigi.png" alt="Luigi icon" />
        <img class="roomCharacter hiddenCharacter" src="assets/images/peach.png" alt="Peach icon" />
    </div>