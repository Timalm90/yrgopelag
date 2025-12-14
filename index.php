<?php
// Require in all PHP files for logic here:
require __DIR__ . "/app/autoload.php";
require __DIR__ . "/app/calendar.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yrgopelag</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/calendar.css">
</head>

<body>

    <?php
    // To do: Require in header and navbar when built
    ?>

    <div class="background">
        <main>
            <?php
            require __DIR__ . "/view/form.php"
            ?>
            <div>

                <article class="calendar whiteBox">

                    <?php
                    myCalendar($booked);
                    ?>
                </article>

                <article class="calendar whiteBox">
                    <?php
                    myCalendar($booked);
                    ?>
                </article>

                <article class="calendar whiteBox">
                    <?php
                    myCalendar($booked);
                    ?>
                </article>
            </div>
        </main>
    </div>

    <?php
    // To do: Require in footer when built
    ?>

</body>

</html>