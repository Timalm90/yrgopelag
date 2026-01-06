<section class="confirmationModal">
    <article class="whiteBox">
        <div class="confirmationText">
            <h2>Dear <?= ucwords(htmlspecialchars($confirmation['visitor'])) ?>,</h2>
            <p>Thank you for choosing <?= ucwords(htmlspecialchars($hotelName)) ?> on <?= ucwords(htmlspecialchars($islandName)) ?>. We're looking forward to your visit!</p>

            <!-- Date info -->
            <?php if ($confirmation['bookingType'] === "Day pass"): ?>
                <p>
                    Your Day pass is valid for <strong><?= htmlspecialchars($confirmation['arrival']) ?></strong>.
                </p>

            <?php else: ?>
                <p>
                    Your visit is registered for <?= htmlspecialchars($confirmation['arrival']) ?> - <?= htmlspecialchars($confirmation['departure']) ?>.<br>
                    Check-in: <?= htmlspecialchars($confirmation['checkinTime']) ?><br>
                    Checkout: <?= htmlspecialchars($confirmation['checkoutTime']) ?>
                </p>
            <?php endif ?>

            <!-- Room info -->
            <?php if ($confirmation['roomName']): ?>
                <p>
                    Your room: <strong><?= ucwords(htmlspecialchars($confirmation['roomName'])) ?></strong>
                </p>
            <?php endif ?>

            <!-- Features -->
            <?php if (!empty($confirmation['features'])): ?>
                <p>Included features: </p>
                <ul>
                    <?php foreach ($confirmation['features'] as $feature): ?>
                        <li>
                            <?= ucwords(htmlspecialchars($feature)) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>

            <!-- Price -->
            <p>Total price: <?= htmlspecialchars($confirmation['totalcost']) ?> credits</p>
            <?php if (!empty($confirmation['discountSum'])): ?>
                <p>
                    You saved <?= htmlspecialchars($confirmation['discountSum']) ?> credits.
                </p>
            <?php endif ?>
        </div>

        <div>
            <img class="confirmationImg" src="assets/images/marioFlag.png" alt="Super Mario iconicly sliding down on flag" />
        </div>

        <button class="modalClose">&times;</button>

    </article>
</section>