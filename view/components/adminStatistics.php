<?php
// THIS IS THE STATISTICS DASHBOARD
$statement = $pdoBooking->prepare("SELECT booking_feature.feature_id, features.feature, COUNT (feature_id) AS 'counts' FROM booking_feature INNER JOIN features ON features.id = feature_id GROUP BY feature_id ORDER BY 'counts' DESC LIMIT 5");
$statement->execute();
$topFeatures = $statement->fetchAll(PDO::FETCH_ASSOC);


$daypass = $pdoBooking->prepare("SELECT COUNT(*) AS daypass FROM bookings WHERE room_id IS NULL");
$daypass->execute();
$daypass = $daypass->fetch(PDO::FETCH_ASSOC);

$bookedRoom = $pdoBooking->prepare("SELECT COUNT(*) AS booked FROM bookings WHERE room_id IS NOT NULL");
$bookedRoom->execute();
$bookedRoom = $bookedRoom->fetch(PDO::FETCH_ASSOC);
?>

<article class="dashboardBox">
    <h2>Number of bookings</h2>
    <p><strong>Room bookings: </strong><?= htmlspecialchars($bookedRoom['booked']) ?></p>
    <p><strong>Day pass: </strong><?= htmlspecialchars($daypass['daypass']) ?></p>
</article>

<article class="dashboardBox">
    <h2>Top 5 popular features</h2>
    <ul>
        <?php foreach ($topFeatures as $topFeature): ?>
            <li>
                <?= htmlspecialchars($topFeature['feature']) ?> (<?= htmlspecialchars($topFeature['counts']) ?>)
            </li>
        <?php endforeach ?>
    </ul>
</article>