<?php

// Buy features, pay centralbank:
// 2, 5, 10 and 17 credits for the four tiers respectively.

?>

<section class="whiteBox">
    <h2>Add features to hotel</h2>
    <form action="../app/admin/addFeatures.php" method="post">
        <!-- Dropdown select which feature to buy/add -->
        <div>
            <label id="itemLabel" for="item">Choose feature to add: </label>
            <select id="item" name="item" required>
                <option value=""> -- Select an item -- </option>
                <?php $allFeatures = findNonActiveFeatures($pdoBooking);
                foreach ($allFeatures as $feature): ?>
                    <option value="<?= htmlspecialchars($feature['id']) ?>">
                        <?= ucwords(htmlspecialchars($feature['feature'])) . ", "  . htmlspecialchars($feature['cost_per_tier']) . " credits" ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <button type="submit">Add feature</button>

        <?php if (isset($_SESSION['addFeature'])) {
            echo $_SESSION['addFeature']; //Feature added successfully
            $_SESSION['addFeature'] = NULL;
        };

        if (isset($_SESSION['adminError'])): ?>
            <ul>
                <?php foreach ($_SESSION['adminError'] as $error): ?>
                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php $_SESSION['adminError'] = NULL;
        endif ?>
    </form>
</section>