<?php
require __DIR__ . "/../app/autoload.php";
require __DIR__ . "/../app/config.php";


// To do: Build the admin site with dashboard that is shown when logged in.
// Here admin should be able to:
// - Change prices in database by prefabricated PDO-connection and query, through input and submit -> DB-query
// - Change number of stars of the hotel
// - Be able to buy more features, connect to API & DB
// - Change avaiable features on booking site
// - Change discounts
// - Show saldo at bank (API connection)

// Require in all files from app/admin
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Yrgopelag</title>
    <link rel="stylesheet" href="../assets/styles/general.css">
    <link rel="stylesheet" href="../assets/styles/admin.css">
    <link rel="stylesheet" href="../assets/styles/nav.css">
</head>

<body>
    <?php
    require __DIR__ . "/nav.php";
    ?>
    <div class="adminBackground">
        <main>
            <!-- IF NOT LOGGED IN -> redirect to login page -->
            <?php if (!isset($_SESSION['admin']))
                header("Location: login.php") ?>


            <!-- IF LOGGED IN: SHOW THIS DASHBOARD... ... ... -->
            <?php
            if (isset($_SESSION['admin'])): ?>
                <!-- DASHBOARD! -->
                <h1>You are logged in! </h1>

            <?php endif; ?>

        </main>
    </div>
</body>

</html>