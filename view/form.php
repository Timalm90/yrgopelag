<section class="bookingForm">
    <article class="form">
        <form action="" method="post">
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
                    <input type="radio" name="room" value="<?= $room['room'] ?>">
                    <label for="<?= $room['room'] ?>"><?= ucwords($room['room']) ?></label>
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
            <!-- Option 1: All features should be registered in DB and choosen in admin. All the choosen features should be included in an array, sorted by category and then tier. The array of features should be foreach looped here -->
            <!-- Option 2: All features are registered in admin and insert into DB if bought. All choosen features are selected in DB, return as array. Foreach looped here  -->

            <div class="field features">
                <label for="features">Features</label>

                <?php
                // !!!!!!!!!!!!!!!! Fetch ALL features, change this to purchased features when decided which!!!!!!!!!!!!!!!!
                $pdoFeatures = $pdo->prepare("SELECT * FROM features");
                $pdoFeatures->execute();
                $features = $pdoFeatures->fetchAll(PDO::FETCH_ASSOC);

                // NEED TO FIND A MUCH BETTER SYSTEM FOR input name HERE, SINCE IT WON'T WORK WITH IDENTIFICATION
                foreach ($features as $feature) : ?>
                    <div>
                        <input type="checkbox" id="<?= $feature['feature']; ?>" name="<?= $feature['feature']; ?>" value="<?= $feature['feature']; ?>">
                        <label for="<?= $feature['feature']; ?>"><?= ucwords($feature['feature']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="field">
                <input type="submit" value="Book!">
            </div>
        </form>
    </article>
</section>