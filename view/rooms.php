    <?php foreach ($rooms as $room): ?>
        <section class="roomSection whiteBox">
            <article class="roomImg">
                <img src="assets/images/<?= $room['room'] ?>.jpg" alt="Picture of <?= $room['room'] ?> room" />
            </article>
            <article class="roomDescription">
                <h2><?= $room['title'] ?></h2>
                <p><?= $room['description'] ?></p>
            </article>
            <article class="roomCalendar calendar whiteBox"><?php myCalendar($booked); ?></article>
        </section>
    <?php endforeach; ?>