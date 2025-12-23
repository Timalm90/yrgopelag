<?php
// Require in all PHP files for logic here:
require __DIR__ . "/app/autoload.php";
require __DIR__ . "/app/rooms.php";
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
    <link rel="stylesheet" href="assets/styles/form.css">
    <link rel="stylesheet" href="assets/styles/calendar.css">
</head>

<body>

    <?php
    require __DIR__ . "/view/components/nav.php";
    ?>

    <div class="background"></div>

    <main>
        <section class="hero whiteBox">
            <div class="hotelStars">
                <img src="assets/images/star.png" alt="Mario star" />
                <img src="assets/images/star.png" alt="Mario star" />
                <img src="assets/images/star.png" alt="Mario star" />
                <img src="assets/images/star.png" alt="Mario star" />
                <img src="assets/images/star.png" alt="Mario star" />
            </div>
            <h1>Welcome to Yoshi's Resort on Starlight Island</h1>
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

        <section class="message">
            <h1>Confirmation or Errors are shown here!</h1>

            <?php
            // Show error messages in errors-array:
            if (!empty($_SESSION['errors'])) : ?>
                <ul>
                    <?php
                    foreach ($_SESSION['errors'] as $error) : ?>
                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php
                //Empty this session variable
                unset($_SESSION['errors']);
            endif;

            if (!empty($_SESSION['success'])) { ?>
                <p>
                    <?php
                    $confirmation = $_SESSION['success']; ?>
                <div class="whitebox">
                    <h2>Dear <?= htmlspecialchars($confirmation['visitor']) ?>,</h2>
                    <p>Thank you for choosing Yoshi's Resort on Starlight Island. We're looking forward to your visit!</p>
                    <p>You're visit is registered for <?= htmlspecialchars($confirmation['arrival']) ?> - <?= htmlspecialchars($confirmation['departure']) ?>. Checkin 15:00 and checkout 11:00.</p>
                    <p>The total prize for your visit is <?= htmlspecialchars($confirmation['totalcost']) ?> credits!</p>
                </div>
                <!-- // var_dump($_SESSION['success']);
                // array(5) { ["visitor"]=> string(4) "Rune" ["arrival"]=> string(10) "2026-01-28" ["departure"]=> string(10) "2026-01-28" ["features"]=> array(1) { [0]=> string(2) "16" } ["totalcost"]=> int(18) } -->
            <?php
                //Empty this session variable
                unset($_SESSION['success']);
            }
            ?>
        </section>

        <?php
        require __DIR__ . "/view/components/form.php"
        ?>

        <div class="showPrice">
            <p>Total price: <span id="totalPrice">0</span> credits</p>
        </div>
    </main>

    <?php
    // To do: Require in footer when built
    ?>

    <script src="assets/scripts/toggleRoom.js"></script>
    <script src="assets/scripts/form.js"></script>
    <script src="assets/scripts/totalprice.js"></script>
    <script src="assets/scripts/generateTransferCode.js"></script>
</body>

</html>