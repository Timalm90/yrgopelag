<article class="dashboardBox addFeatureBox">
    <h3>Add features to hotel</h3>

    <div>
        <!-- Dropdown select which feature to buy/add -->
        <label>Choose feature to add:</label>
        <br>
        <select class="featureSelect" name="item" required>
            <option value=""> -- Select an item -- </option>

            <?php
            foreach ($allFeatures as $feature): ?>
                <option value="<?= htmlspecialchars($feature['id']) ?>">
                    <?= ucwords(htmlspecialchars($feature['feature'])) . ", " . htmlspecialchars($feature['cost_per_tier']) . " credits" ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="centerButton">
        <button class="adminButton addFeatureButton requireMastercode">Add feature</button>
    </div>
</article>