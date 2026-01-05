<?php
require __DIR__ . "/../app/autoload.php";
require __DIR__ . "/../app/config.php";


//  IF NOT LOGGED IN -> redirect to login page 
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); //IN LOCALHOST
    // header("Location: /MAPP/view/login.php"); //IN DEPLOY
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Yrgopelag</title>
    <link rel="stylesheet" href="../assets/styles/general.css">
    <link rel="stylesheet" href="../assets/styles/nav.css">
    <link rel="stylesheet" href="../assets/styles/admin.css">
</head>

<body>
    <?php
    require __DIR__ . "/components/nav.php";
    ?>
    <div class="adminBackground"></div>

    <main>
        <section class="whiteBox">

            <!-- IF LOGGED IN: SHOW THIS DASHBOARD... ... ... -->
            <?php
            if (isset($_SESSION['admin'])): ?>
                <!-- DASHBOARD! -->
                <h1>Welcome, <?= ucwords(htmlspecialchars(($_SESSION['admin']['name']))) ?>!</h1>

                <section class="adminMessage">
                    <!-- Show confirmation or error messages here! -->
                    <?php if (isset($_SESSION['adminSuccess'])): ?>
                        <article class="confirmationAdminBox">
                            <?= $_SESSION['adminSuccess']; ?>
                        </article>
                    <?php $_SESSION['adminSuccess'] = NULL;
                    endif; ?>

                    <?php
                    // ERROR-MESSAGE
                    if (isset($_SESSION['adminErrors'])): ?>
                        <article class="errorAdminBox">
                            <ul>
                                <?php foreach ($_SESSION['adminErrors'] as $error): ?>
                                    <li>
                                        <?= htmlspecialchars($error) ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </article>
                    <?php $_SESSION['adminErrors'] = NULL;
                    endif ?>
                </section>

                <section class="dashboard">
                    <?php require __DIR__ . "/components/aboutHotel.php"; ?>
                    <?php require __DIR__ . "/components/adminStatistics.php"; ?>
                </section>

                <section class="dashboard settingsDashboard">
                    <?php require __DIR__ . "/components/checkBalance.php"; ?>
                    <?php require __DIR__ . "/components/adminSettings.php"; ?>
                    <?php require __DIR__ . "/components/adminFinancials.php"; ?>

                </section>

                <section class="dashboard adminDashboard">
                    <!-- Control admins -->
                    <h2>This is the admin dashboard</h2>
                </section>

            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/scripts/admin.js"></script>
    <!-- Shows current room/feature price when admin attempts to change it -->
    <script src="../assets/scripts/changePrice.js"></script>
</body>

</html>