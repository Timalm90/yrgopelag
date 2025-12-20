    <?php foreach ($rooms as $room):
        $booked = bookedDays($bookedByRoom[$room['id']]);
    ?>
        <section class="roomSection whiteBox">
            <article class="roomImg">
                <img src="assets/images/<?= $room['room'] ?>.jpg" alt="Picture of <?= $room['room'] ?> room" />
            </article>
            <article class="roomDescription">
                <h2><?= $room['title'] ?></h2>
                <p><?= $room['description'] ?></p>
            </article>
            <article class="roomCalendar">
                <p>Jan 2026</p>
                <div class="calendar">
                    <?php
                    require __DIR__ . '/calendar.php'; ?>
                </div>
            </article>
        </section>
    <?php endforeach; ?>