<article class="dashboardBox addFeatureBox">
    <h3>Add features to hotel</h3>

    <form action="../app/admin/addFeatures.php" method="post" class="addFeatureForm">
        <div class="boxContent">
            <!-- Dropdown select which feature to buy/add -->
            <div>
                <label>Choose feature to add:</label>
                <br>
                <select class="featureSelect" name="item" required>
                    <option value=""> -- Select an item -- </option>

                    <?php
                    // $allFeatures = findNonActiveFeatures($pdoBooking);
                    foreach ($allFeatures as $feature): ?>
                        <option value="<?= htmlspecialchars($feature['id']) ?>">
                            <?= ucwords(htmlspecialchars($feature['feature'])) . ", " . htmlspecialchars($feature['cost_per_tier']) . " credits" ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="centerButton">
            <button class="adminButton addFeatureButton" type="submit">Add feature</button>
        </div>
    </form>
</article>