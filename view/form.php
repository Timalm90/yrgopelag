<section class="bookingForm">
    <article class="form">
        <form action="/app/booking.php" method="post">
            <!-- The for attribute of the <label> tag should be equal to the id attribute of the <input> element to bind them together. -->
            <div class="field">
                <!-- Name -->
                <label for="name">Name:</label>
                <br>
                <input type="test" name="name" placeholder="Enter your name">
            </div>

            <div class="field">
                <!-- TransferCode, could be changed to API-key to retrieve transferCode backend -->
                <label for="transferCode">transferCode:</label>
                <br>
                <input type="password" name="transferCode" placeholder="Enter your transferCode">
            </div>

            <div class="field">
                <!-- Room type could be fetched from DB? Could also be checkbox with a limit to only choose 1? -->
                <label for="room">Choose room:</label>

                <?php
                $pdoRoom = $pdo->prepare("SELECT * FROM rooms");
                $pdoRoom->execute();
                $rooms = $pdoRoom->fetchAll(PDO::FETCH_ASSOC); ?>

                <?php
                foreach ($rooms as $room): ?>
                    <div>
                        <input type="radio" id="room_<?= $room['id'] ?>" name="room" value="<?= $room['id'] ?>">

                        <label for="room_<?= $room['id'] ?>"><?= ucwords($room['room']) ?> (<?= $room['price_per_night'] ?> / night)</label>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="field">
                <!-- Choose arrival date -->
                <label for="arrival">Arrival:</label>
                <br>
                <input type="date" name="arrivalDate" min="2026-01-01" max="2026-01-31">
            </div>

            <div class="field">
                <!-- Choose departure date -->
                <label for="departure">Departure:</label>
                <br>
                <input type="date" name="departureDate" min="2026-01-01" max="2026-01-31">
            </div>

            <!-- FEATURES -->
            <div class="field features">
                <label for="features">Features</label>
                <?php
                // !!!!!!!!!!!!!!!! Fetch ALL features, change this to purchased features when decided which!!!!!!!!!!!!!!!!
                $pdoFeatures = $pdo->prepare("SELECT * FROM features");
                // $pdoFeatures = $pdo->prepare("SELECT * FROM features WHERE purchased_feature = 1");
                $pdoFeatures->execute();
                $features = $pdoFeatures->fetchAll(PDO::FETCH_ASSOC);

                foreach ($features as $feature) : ?>
                    <div>
                        <input type="checkbox" id="feature_<?= $feature['id']; ?>" name="features[]" value="<?= $feature['id']; ?>">
                        <label for="feature_<?= $feature['id']; ?>"><?= ucwords($feature['feature']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="field">
                <input type="submit" value="Book!">
            </div>
        </form>
    </article>
</section>