<article class="dashboardBox changePriceBox">
    <h3>Change hotel prices</h3>
    <div class="boxContent">
        <!-- Select category (room or tier) -->
        <div>
            <label for="category">Choose category:</label>
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
            <input type="number" id="priceInput" name="price" placeholder="Enter new price" required>
        </div>
    </div>

    <div class="centerButton">
        <button class="adminButton requireMastercode" id="changePriceBtn">Change price</button>
    </div>
</article>