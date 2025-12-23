<section class="bookingForm">
    <!-- Toogle: Book room/Day pass, changes form content -->
    <article class="toggleBooking">
        <div class="togglePill">
            <input type="radio" id="bookRoom" name="bookingType" value="room" checked>
            <label for="bookRoom">Book Room</label>

            <input type="radio" id="dayPass" name="bookingType" value="day">
            <label for="dayPass">Day Pass</label>

            <div class="slider"></div>
        </div>
    </article>
    <form action="/app/booking.php" method="post">
        <!-- BOOK ROOM -->
        <fieldset id="roomSection">
            <legend class="legendRoom">Choose Room and Date</legend>

            <div class="field togglable">
                <?php
                $rooms = getRooms($pdoBooking);

                foreach ($rooms as $room): ?>
                    <div>
                        <input type="radio" id="room_<?= htmlspecialchars($room['id']) ?>" name="room" value="<?= htmlspecialchars($room['id']) ?>">

                        <label for="room_<?= htmlspecialchars($room['id']) ?>"><?= ucwords(htmlspecialchars($room['room'])) ?> (<?= htmlspecialchars($room['price_per_night']) ?>c/night)</label>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="field">
                <!-- Choose arrival date -->
                <label class="arrivalLabel" for="arrivalDate">Arrival:</label>
                <br>
                <input type="date" id="arrivalDate" name="arrivalDate" min="2026-01-01" max="2026-01-31">
            </div>

            <div class="field togglable">
                <!-- Choose departure date -->
                <label for="departureDate">Departure:</label>
                <br>
                <input type="date" id="departureDate" name="departureDate" min="2026-01-01" max="2026-01-31">
            </div>
        </fieldset>

        <!-- BOOK FEATURES -->
        <fieldset class="field features">
            <legend>Features</legend>
            <?php
            $features = getActiveFeatures($pdoBooking);

            $featurePrice = getFeaturePrices($pdoBooking);

            foreach ($features as $feature) :
                // FETCH PRICE FOR FEATURE
                $priceFeature = $featurePrice[$feature['id']];
            ?>
                <div>
                    <input type="checkbox" id="feature_<?= htmlspecialchars($feature['id']); ?>" name="features[]" value="<?= htmlspecialchars($feature['id']); ?>">
                    <label for="feature_<?= htmlspecialchars($feature['id']); ?>"><?= ucwords(htmlspecialchars($feature['feature'])); ?>
                        (<?= htmlspecialchars($priceFeature) ?>c)
                    </label>
                </div>
            <?php endforeach; ?>
        </fieldset>

        <!-- GUEST INFO & PAYMENT -->
        <fieldset>
            <legend>Guest info & Payment</legend>
            <div class="field">
                <!-- Name -->
                <label for="name">Name:</label>
                <br>
                <input type="text" id="name" name="name" placeholder="Enter your name" autocomplete="name">
            </div>

            <div class="field">
                <!-- API-key for transferCode service -->
                <label for="apiKey">API Key:</label><br>
                <input type="password" id="apiKey" name="apiKey" placeholder="Enter your API key">
            </div>
            <div class="field">
                <button type="button" id="getTransferCode">Get Transfer Code</button>
            </div>

            <div class="field">
                <div class="field">
                    <p>Don't want to enter your API key? Visit the
                        <a href="https://www.yrgopelag.se/centralbank" target="_blank">
                            Central Bank
                        </a> to retrieve your transfer code, and enter it below:
                    </p>
                    <!-- transferCode (manually or through service) -->
                    <label for="transferCode">Transfer Code (optional):</label><br>
                    <input type="password" id="transferCode" name="transferCode" placeholder="Enter your transfer code">
                </div>
            </div>
        </fieldset>

        <div class="field">
            <input type="submit" value="Book!">
        </div>
    </form>
</section>