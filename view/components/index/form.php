<div class="formWrapper">
    <article class="showPrice">
        <div class="showPrice">
            <img src="assets/images/coin.png" alt="golden coin" />
            <p>Total price: <br><span id="totalPrice">0</span> credits</p>
        </div>
    </article>

    <section class="bookingForm whiteBox">
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
        <form action="app/booking.php" method="post">
            <!-- BOOK ROOM -->
            <fieldset id="roomSection">
                <legend class="legendRoom">Choose Room and Date</legend>
                <article class="roomSection">

                    <div class="field rooms togglable">
                        <div class="innerContainer">

                            <p>Rooms: </p>
                            <?php
                            foreach ($rooms as $room): ?>
                                <div>
                                    <input type="radio" id="room_<?= htmlspecialchars($room['id']) ?>" name="room" value="<?= htmlspecialchars($room['id']) ?>">

                                    <label for="room_<?= htmlspecialchars($room['id']) ?>"><?= ucwords(htmlspecialchars($room['room'])) ?> <span class="priceSpan">(<?= htmlspecialchars($room['price_per_night']) ?>c/night)</span></label>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>

                    <div class="field arrival">
                        <div class="innerContainer">
                            <!-- Choose arrival date -->
                            <label class="arrivalLabel" for="arrivalDate">Arrival:</label>
                            <br>
                            <input type="date" id="arrivalDate" name="arrivalDate" min="2026-01-01" max="2026-01-31">
                        </div>
                    </div>

                    <div class="field departure departureBox togglable">
                        <div class="innerContainer">
                            <!-- Choose departure date -->
                            <label for="departureDate">Departure:</label>
                            <br>
                            <input type="date" id="departureDate" name="departureDate" min="2026-01-01" max="2026-01-31">
                        </div>
                    </div>
                </article>
            </fieldset>

            <!-- BOOK FEATURES -->
            <fieldset class="field features">
                <legend>Features
                    <img class="secretBox" src="assets/images/secretBox.png" />
                </legend>

                <article class="featureGrid">
                    <?php
                    foreach ($categories as $category): ?>
                        <div class="featureCategory">
                            <p class="categoryName">
                                <?= ucwords(htmlspecialchars($category['category'])); ?>
                            </p>
                            <?php
                            $featureByCategory = featureByCategory($pdoBooking, $category['id']);

                            foreach ($featureByCategory as $feature): ?>
                                <div>

                                    <input type="checkbox" id="feature_<?= htmlspecialchars($feature['id']); ?>" name="features[]" value="<?= htmlspecialchars($feature['id']); ?>">
                                    <label for="feature_<?= htmlspecialchars($feature['id']); ?>"><?= ucwords(htmlspecialchars($feature['feature'])); ?>
                                        <span class="priceSpan">(<?= htmlspecialchars($feature['price_per_feature']) ?>c)</span>
                                    </label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php
                    endforeach
                    ?>
                </article>
            </fieldset>

            <!-- GUEST INFO & PAYMENT -->
            <fieldset>
                <legend>Guest info & Payment</legend>
                <article class="paymentSection">
                    <div class="field">
                        <!-- Name -->
                        <label for="name">Name:</label>
                        <br>
                        <input type="text" id="name" name="name" placeholder="Enter your name" autocomplete="name">
                    </div>

                    <div class="field">
                        <!-- API-key for transferCode service -->
                        <label for="apiKey">API Key: *</label><br>
                        <input type="password" id="apiKey" name="apiKey" placeholder="Enter your API key">
                    </div>
                    <div class="field">
                        <button type="button" id="getTransferCode">Get Transfer Code</button>
                    </div>
                </article>
                <p class="APIkeyForm">*Don't want to enter your API key? Visit the
                    <a href="https://www.yrgopelag.se/centralbank" target="_blank">
                        Central Bank
                    </a> to retrieve your transfer code, and enter it below:
                </p>
                <p id="transferCodeError"></p>

                <div class="field transferCodeInput">
                    <!-- transferCode (manually or through service) -->
                    <label for="transferCode">Transfer Code:</label><br>
                    <input type="password" id="transferCode" name="transferCode" placeholder="Enter your transfer code">
                </div>

            </fieldset>

            <div class="field bookButton">
                <input type="submit" id="bookButton" value="Book!">
            </div>
        </form>
    </section>
</div>