<?php
$rooms = getRooms($pdoBooking);
$tiers = getTierLevels($pdoBooking);
?>


<article class="whiteBox dashboardBox">
    <h2>Check balance</h2>

    <button id="checkBalanceBtn" class="adminButton">Check!</button>
    <p id="balanceOutput" class="adminMessage"></p>
</article>

<article class="whiteBox dashboardBox changePriceBox">
    <h2>Change hotel prices</h2>

    <form action="../app/admin/changePrice.php" method="post">

        <!-- Select category (room or tier) -->
        <div>
            <label for="category">Would you like to change a room price or a tier price?</label>
            <select id="category" name="category" required>
                <option value=""> -- Choose -- </option>
                <option value="room">Room</option>
                <option value="tier">Tier</option>
            </select>
        </div>

        <!-- Select item (rooms or tiers) -->
        <div>
            <label for="item">Choose item:</label>
            <select id="item" name="item" required>
                <option value=""> -- Select category first -- </option>

                <!-- Rooms -->
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['room']) ?>" data-category="room" hidden>
                        <?= ucwords(htmlspecialchars($room['room'])) ?>
                    </option>
                <?php endforeach; ?>

                <!-- Tiers -->
                <?php foreach ($tiers as $tier): ?>
                    <option value="<?= htmlspecialchars($tier['tier']) ?>" data-category="tier" hidden>
                        <?= ucwords(htmlspecialchars($tier['tier'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Current price -->
        <div>
            <p>Current price: <span id="currentPrice"></span></p>
        </div>

        <!-- New price -->
        <div>
            <label for="price">New price:</label>
            <input type="number" name="price" placeholder="Enter new price" required>
        </div>

        <button type="submit">Change price</button>

        <!-- Show server-side messages -->
        <?php if (isset($_SESSION['updatePrice'])): ?>
            <p><?= $_SESSION['updatePrice'];
                $_SESSION['updatePrice'] = null; ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['adminErrors'])): ?>
            <ul>
                <?php foreach ($_SESSION['adminErrors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php $_SESSION['adminErrors'] = null; ?>
        <?php endif; ?>

    </form>
</article>