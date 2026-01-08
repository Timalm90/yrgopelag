<?php
require __DIR__ . "/../app/autoload.php";

// If not logged in, redirect to login page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); //IN LOCALHOST
    // header("Location: /yrgopelag/view/login.php"); //IN DEPLOY
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

            <!-- If logged in, show dashboard-->
            <?php
            if (isset($_SESSION['admin'])): ?>
                <h1>Welcome, <?= ucwords(htmlspecialchars(($_SESSION['admin']['name']))) ?>!</h1>

                <!-- Dashboard Menu -->
                <section class="dashboardMenu">
                    <button class="active">Overview</button>
                    <button>Statistics</button>
                    <button>Financial</button>
                    <button>Admin</button>
                </section>

                <!-- Confirmation or error messages -->
                <section class="adminMessage">
                    <article id="adminMessageBox" class="adminHidden"></article>
                </section>

                <!-- DASHBOARD: Overview -->
                <section class="dashboard overviewLayout">
                    <?php
                    require __DIR__ . "/components/admin/aboutHotel.php";
                    require __DIR__ . "/components/admin/addFeature.php";
                    ?>
                </section>

                <!-- DASHBOARD: Statistics -->
                <section class="dashboard statisticsLayout adminHidden">
                    <?php require __DIR__ . "/components/admin/statistics.php";
                    ?>
                </section>

                <!-- DASHBOARD: Financial -->
                <section class="dashboard financialLayout adminHidden">
                    <?php
                    require __DIR__ . "/components/admin/checkBalance.php";
                    require __DIR__ . "/components/admin/financials.php";
                    ?>
                </section>

                <!-- DASHBOARD: Admin -->
                <section class="dashboard adminLayout adminHidden">
                    <?php
                    require __DIR__ . "/components/admin/createAdmin.php";
                    require __DIR__ . "/components/admin/changePassword.php"; ?>
                </section>

            <?php endif; ?>
        </section>
    </main>

    <!-- Mastercode modal -->
    <!-- Modal -->
    <section id="mastercodeModal" class="mastercodeModal adminHidden">
        <article class="mastercodeModalContent">
            <h3>Enter Mastercode</h3>
            <input type="password" id="mastercodeInput" placeholder="Mastercode">
            <button id="submitMastercode">Authorize</button>
            <button id="cancelMastercode">Cancel</button>
        </article>
    </section>

    <script src="../assets/scripts/functions.js"></script>
    <script src="../assets/scripts/admin/dashboard.js"></script>
    <script src="../assets/scripts/admin/financials.js"></script>
    <script src="../assets/scripts/admin/users.js"></script>
</body>

</html>