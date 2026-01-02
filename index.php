<?php
// Require in all PHP files for logic here:
require __DIR__ . "/app/autoload.php";
require __DIR__ . "/app/rooms.php";

$starRating = getSettingsValue($pdoBooking, 'star_rating');
$starRating = (int) $starRating;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yrgopelag</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/styles/general.css">
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/nav.css">
    <link rel="stylesheet" href="assets/styles/togglebar.css">
    <link rel="stylesheet" href="assets/styles/rooms.css">
    <link rel="stylesheet" href="assets/styles/error.css">
    <link rel="stylesheet" href="assets/styles/form.css">
    <link rel="stylesheet" href="assets/styles/calendar.css">
    <link rel="stylesheet" href="assets/styles/modal.css">
</head>

<body>

    <?php
    require __DIR__ . "/view/components/nav.php";
    require __DIR__ . "/view/components/offers.php";
    ?>

    <div class="background"></div>

    <main>
        <section class="hero whiteBox">
            <div class="hotelStars">
                <?php for ($i = 0; $i < $starRating; $i++): ?>
                    <img src="assets/images/star.png" alt="Mario star" />
                <?php endfor ?>
            </div>
            <h1>Welcome to <?= ucwords(htmlspecialchars(getSettingsValue($pdoBooking, 'hotel_name'))) ?> on <?= ucwords(htmlspecialchars(getSettingsValue($pdoBooking, 'island_name'))) ?></h1>
            <h2> - where magic, adventure, and luxury meet!</h2>
            <p>Experience a one-of-a-kind stay filled with fun, relaxation, and surprises. Whether you want to unwind in our luxurious Princess Peach Suite, challenge friends in exciting activities, or just enjoy a day at the island's most spectacular features - your next adventure awaits at Yoshi's Resort!</p>
        </section>
        <section class="roomToggle togglePill" role="tablist">
            <button class="roomToggleBtn" data-room="0" role="tab">
                Budget
            </button>
            <button class="roomToggleBtn active" data-room="1" role="tab" aria-selected="true">
                Standard
            </button>
            <button class="roomToggleBtn" data-room="2" role="tab">
                Luxury
            </button>
        </section>

        <?php require __DIR__ . "/view/components/rooms.php"; ?>

        <?php
        // Show error messages in errors-array:
        if (!empty($_SESSION['errors'])) : ?>
            <section class="errorMessage">
                <h2>Error!</h2>
                <ul>
                    <?php
                    foreach ($_SESSION['errors'] as $error) : ?>
                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php
            //Empty this session variable
            $_SESSION['errors'] = NULL;
        endif; ?>


        <?php
        require __DIR__ . "/view/components/form.php";
        ?>

        <!-- <div class="showPrice">
            <img src="assets/images/coin.png" alt="golden coin" />
            <p>Total price: <span id="totalPrice">0</span> credits</p>
        </div> -->

    </main>

    <?php
    // To do: Require in footer when built
    ?>

    <?php require __DIR__ . "/view/components/featureModal.php"; ?>
    <!-- ------------------------------------------ SUCCESS MODAL ------------------------------------------ -->
    <?php
    if (!empty($_SESSION['success'])) { ?>
        <section class="modal">
            <article class="whiteBox">
                <?php $confirmation = $_SESSION['success']; ?>
                <h2>Dear <?= ucwords(htmlspecialchars($confirmation['visitor'])) ?>,</h2>
                <p>Thank you for choosing <?= ucwords(htmlspecialchars(getSettingsValue($pdoBooking, 'hotel_name'))) ?> on <?= ucwords(htmlspecialchars(getSettingsValue($pdoBooking, 'island_name'))) ?>. We're looking forward to your visit!</p>

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

                <button class="modalClose">&times;</button>

            </article>
        </section>

    <?php
        //Empty this session variable
        $_SESSION['success'] = NULL;
    }
    ?>

    <script src="assets/scripts/toggleRoom.js"></script>
    <script src="assets/scripts/form.js"></script>
    <script src="assets/scripts/totalprice.js"></script>
    <script src="assets/scripts/generateTransferCode.js"></script>
    <script src="assets/scripts/modal.js"></script>
</body>

</html>