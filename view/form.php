<section class="bookingForm">
    <article class="form">
        <form action="/app/booking.php" method="post">

            <!-- Toogle: Book room/Day pass, changes form content -->
            <div class="booking-toggle">
                <input type="radio" id="bookRoom" name="bookingType" value="room" checked>
                <label for="bookRoom">Book Room</label>

                <input type="radio" id="dayPass" name="bookingType" value="day">
                <label for="dayPass">Day Pass</label>

                <div class="slider"></div>
            </div>

            <!-- BOOK ROOM -->
            <fieldset id="roomSection">
                <legend class="legendRoom">Choose Room and Date</legend>

                <div class="field togglable">
                    <?php
                    $rooms = getRooms($pdo);

                    foreach ($rooms as $room): ?>
                        <div>
                            <input type="radio" id="room_<?= $room['id'] ?>" name="room" value="<?= $room['id'] ?>">

                            <label for="room_<?= $room['id'] ?>"><?= ucwords($room['room']) ?> (<?= $room['price_per_night'] ?> / night)</label>
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
                $features = getPurchasedFeatures($pdo);

                $featurePrice = getFeaturePrices($pdo);

                foreach ($features as $feature) :
                    // FETCH PRICE FOR FEATURE
                    $priceFeature = $featurePrice[$feature['id']];
                ?>
                    <div>
                        <input type="checkbox" id="feature_<?= $feature['id']; ?>" name="features[]" value="<?= $feature['id']; ?>">
                        <label for="feature_<?= $feature['id']; ?>"><?= ucwords($feature['feature']); ?>
                            (<?= $priceFeature ?> credits)
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
    </article>
</section>