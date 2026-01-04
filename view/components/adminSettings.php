<?php
// THIS IS THE ADMIN SETTINGS DASHBOARD
// Buy features, pay centralbank:
// 2, 5, 10 and 17 credits for the four tiers respectively.

//About hotel
$owner = getSettingsValue($pdoBooking, "hotel_owner");
$islandName = getSettingsValue($pdoBooking, "island_name");
$hotelName = getSettingsValue($pdoBooking, "hotel_name");
$numberOfStars = getSettingsValue($pdoBooking, "star_rating");
?>

<article class="dashboardBox">
    <h2>About hotel</h2>
    <p><strong>Island: </strong><?= htmlspecialchars($islandName) ?></p>
    <p><strong>Hotel: </strong><?= htmlspecialchars($hotelName) ?></p>
    <p><strong>Stars: </strong><?= htmlspecialchars($numberOfStars) ?></p>
    <p><strong>Owner: </strong><?= htmlspecialchars($owner) ?></p>
</article>

<article class="dashboardBox addFeatureBox">
    <h2>Add features to hotel</h2>

    <form action="../app/admin/addFeatures.php" method="post" class="addFeatureForm">

        <!-- Dropdown select which feature to buy/add -->
        <div>
            <label>Choose feature to add:</label>

            <select class="featureSelect" name="item" required>
                <option value=""> -- Select an item -- </option>

                <?php
                $allFeatures = findNonActiveFeatures($pdoBooking);
                foreach ($allFeatures as $feature): ?>
                    <option value="<?= htmlspecialchars($feature['id']) ?>">
                        <?= ucwords(htmlspecialchars($feature['feature'])) . ", " . htmlspecialchars($feature['cost_per_tier']) . " credits" ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="addFeatureButton">
            <button class="adminButton" type="submit">Add feature</button>
        </div>

    </form>
</article>