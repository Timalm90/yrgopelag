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
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/nav.css">
    <link rel="stylesheet" href="assets/styles/rooms.css">
    <link rel="stylesheet" href="assets/styles/form.css">
    <link rel="stylesheet" href="assets/styles/calendar.css">
</head>

<body>

    <?php
    require __DIR__ . "/view/nav.php";
    ?>

    <div class="background">
        <main>
            <section class="roomToggle" role="tablist" aria-label="Välj rumstyp">
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

            <?php require __DIR__ . "/view/rooms.php"; ?>
            <?php
            require __DIR__ . "/view/form.php"
            ?>

            <section class="message">
                <h1>Confirmation or Errors are shown here!</h1>

                <?php
                // Show error messages in errors-array:
                if (!empty($_SESSION['errors'])) : ?>
                    <ul>
                        <?php
                        foreach ($_SESSION['errors'] as $error) : ?>
                            <li>
                                <?= $error ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php
                    //Empty this session variable
                    unset($_SESSION['errors']);
                endif;

                if (!empty($_SESSION['success'])) { ?>
                    <p>
                        <?= $_SESSION['success']; ?>
                    </p>
                <?php
                    //Empty this session variable
                    unset($_SESSION['success']);
                }
                ?>
            </section>
            <div class="showPrice">
                <p>Total price: <span id="totalPrice">0</span> credits</p>
            </div>
        </main>
    </div>

    <?php
    // To do: Require in footer when built
    ?>

    <script src="assets/scripts/toggleRoom.js"></script>
    <script src="assets/scripts/form.js"></script>
    <script src="assets/scripts/totalprice.js"></script>
    <script src="assets/scripts/generateTransferCode.js"></script>
</body>

</html>