<?php
// Require in all PHP files for logic here:
require __DIR__ . "/app/autoload.php";
require __DIR__ . "/app/rooms.php";

// $starRating = getSettingsValue($pdoBooking, 'star_rating'); //
// $starRating = (int) $starRating;
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
    <link rel="stylesheet" href="assets/styles/footer.css">
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
                <?php for ($i = 0; $i < (int)$starRating; $i++): ?>
                    <img src="assets/images/star.png" alt="Mario star" />
                <?php endfor ?>
            </div>
            <h1>Welcome to <?= ucwords(htmlspecialchars($hotelName)) ?> on <?= ucwords(htmlspecialchars($islandName)) ?></h1>
            <h2> - where magic, adventure, and luxury meet!</h2>
            <p>Experience a one-of-a-kind stay filled with fun, relaxation, and surprises. Whether you want to unwind in our luxurious Princess Peach Suite, challenge friends in exciting activities, or just enjoy a day at the island's most spectacular features - your next adventure awaits at Yoshi's Resort!</p>
            <img class="heroImg" src="assets/images/yoshiLeft.png" alt="Walking happy Yoshi" />
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
            <section class="sectionError">
                <img class="errorImg errorGoomba" src="assets/images/goombaLeft.png" alt="Walking Goomba" />

                <article class="errorMessage" id="errors">
                    <h2>Error!</h2>
                    <ul>
                        <?php
                        foreach ($_SESSION['errors'] as $error) : ?>
                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </article>

                <img class="errorImg errorPiranha" src="assets/images/piranha.png" alt="Piranha plant" />
            </section>
        <?php
            //Empty this session variable
            $_SESSION['errors'] = NULL;
        endif; ?>


        <?php
        require __DIR__ . "/view/components/form.php";
        ?>

    </main>

    <?php
    require __DIR__ . "/view/components/footer.php";
    ?>

    <?php require __DIR__ . "/view/components/featureModal.php"; ?>
    <!-- ------------------------------------------ CONFIRMATION MODAL ------------------------------------------ -->
    <?php
    if (!empty($_SESSION['success'])) {
        $confirmation = $_SESSION['success'];

        require __DIR__ . "/view/components/confirmationModal.php";

        //Empty this session variable
        $_SESSION['success'] = NULL;
    }
    ?>

    <script src="assets/scripts/toggleRoom.js"></script>
    <script src="assets/scripts/totalprice.js"></script>
    <script src="assets/scripts/calendar.js"></script>
    <script src="assets/scripts/form.js"></script>
    <script src="assets/scripts/generateTransferCode.js"></script>
    <script src="assets/scripts/modal.js"></script>
</body>

</html>