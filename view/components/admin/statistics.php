<article class="dashboardBox">
    <h3>Number of bookings</h3>
    <p><strong>Room bookings: </strong><?= htmlspecialchars($bookedRoomCount['booked']) ?></p>
    <p><strong>Day pass: </strong><?= htmlspecialchars($daypassCount['daypass']) ?></p>
</article>

<article class="dashboardBox">
    <h3>Top 5 popular features</h3>
    <ol>
        <?php foreach ($topFeatures as $topFeature): ?>
            <li>
                <?= ucwords(htmlspecialchars($topFeature['feature'])) ?> (<?= htmlspecialchars($topFeature['counts']) ?>)
            </li>
        <?php endforeach ?>
    </ol>
</article>