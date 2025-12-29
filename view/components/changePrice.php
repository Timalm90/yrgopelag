<section class="whiteBox">
    <h2>Change hotel prices</h2>
    <form action="../app/admin/changePrice.php" method="post">
        <!-- Select category (room or tier) -->
        <div>
            <label for="category">Would you like to change a room price or price of tier level?</label>
            <select id="category" name="category" required>
                <option value=""> -- Choose -- </option>
                <option value="room">Room</option>
                <option value="tier">Tier</option>
            </select>
        </div>

        <!-- Dropdown select which item in choosen category, empty if no category choosen -->
        <div>
            <label id="itemLabel" for="item">Choose item: </label>
            <select id="item" name="item">
                <option value=""> -- Select category first -- </option>

                <?php
                $rooms = getRooms($pdoBooking);
                $tiers = getTierLevels($pdoBooking);

                // Rooms:
                foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['room']) ?>" data-category="room"
                        hidden>
                        <?= ucwords(htmlspecialchars($room['room'])) ?>
                    </option>

                <?php
                endforeach;
                // Tiers: 
                foreach ($tiers as $tier): ?>
                    <option value="<?= htmlspecialchars($tier['tier']) ?>" data-category="tier"
                        hidden>
                        <?= ucwords(htmlspecialchars($tier['tier'])) ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <!-- Enter price (integer) -->
        <div>
            <p>Current price: <span id="currentPrice"> </span></p>
        </div>
        <div>
            <label for="price">New price:</label>
            <input type="number" name="price" placeholder="Enter new price" required>
        </div>

        <button type="submit">Change price</button>

        <?php if (isset($_SESSION['updatePrice'])) {
            echo $_SESSION['updatePrice'];
            $_SESSION['updatePrice'] = NULL;
        }

        if (isset($_SESSION['adminErrors'])): ?>
            <ul>
                <?php foreach ($_SESSION['adminErrors'] as $error): ?>
                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php $_SESSION['adminErrors'] = NULL;
        endif ?>
    </form>
</section>