<?php
require __DIR__ . "/../app/autoload.php";
require __DIR__ . "/../app/config.php";

//  IF NOT LOGGED IN -> redirect to login page 
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); //IN LOCALHOST
    // header("Location: /MAPP/view/login.php"); //IN DEPLOY
    exit;
}

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
    require __DIR__ . "/components/nav.php";
    ?>
    <div class="adminBackground"></div>
    <main>
        <!-- IF LOGGED IN: SHOW THIS DASHBOARD... ... ... -->
        <?php
        if (isset($_SESSION['admin'])): ?>
            <!-- DASHBOARD! -->
            <h1>Welcome, <?= ucwords(htmlspecialchars(($_SESSION['admin']['name']))) ?>!</h1>

        <?php endif; ?>

        <?php require __DIR__ . "/components/checkBalance.php"; ?>

        <?php require __DIR__ . "/components/changePrice.php"; ?>

        <?php require __DIR__ . "/components/addFeatures.php"; ?>

    </main>
    <script src="../assets/scripts/changePrice.js"></script>
</body>

</html>