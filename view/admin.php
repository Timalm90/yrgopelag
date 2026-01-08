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
    <link rel="stylesheet" href="../assets/styles/admin/admin.css">
    <link rel="stylesheet" href="../assets/styles/admin/dashboard.css">
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
                <section class="welcome">
                    <img src="../assets/images/star.png" alt="Super Mario star" />
                    <h1>Welcome, <?= ucwords(htmlspecialchars(($_SESSION['admin']['name']))) ?>!</h1>
                    <img src="../assets/images/star.png" alt="Super Mario star" />
                </section>

                <!-- Dashboard Menu -->
                <section class=" dashboardMenu">
                    <button class="active">Overview</button>
                    <button>Statistics</button>
                    <button>Financial</button>
                    <button>Admin</button>
                </section>

                <!-- Confirmation or error messages -->
                <section class="adminMessage">
                    <article class="adminMessageBox adminHidden"></article>
                </section>

                <!-- DASHBOARD: Overview -->
                <section class="dashboard overviewLayout">
                    <img src="../assets/images/yoshiLeft.png" alt="Walking Yoshi" />
                    <?php
                    require __DIR__ . "/components/admin/aboutHotel.php";
                    require __DIR__ . "/components/admin/addFeature.php";
                    ?>
                </section>

                <!-- DASHBOARD: Statistics -->
                <section class="dashboard statisticsLayout adminHidden">
                    <?php require __DIR__ . "/components/admin/statistics.php";
                    ?>
                    <img src="../assets/images/sittingToad.png" alt="Toad" />
                </section>

                <!-- DASHBOARD: Financial -->
                <section class="dashboard financialLayout adminHidden">
                    <?php
                    require __DIR__ . "/components/admin/checkBalance.php";
                    require __DIR__ . "/components/admin/financials.php";
                    ?>
                    <img src="../assets/images/coinPile.png" alt="Pile of Super Mario coins" />
                </section>

                <!-- DASHBOARD: Admin -->
                <section class="dashboard adminLayout adminHidden">
                    <?php
                    require __DIR__ . "/components/admin/createAdmin.php";
                    require __DIR__ . "/components/admin/changePassword.php"; ?>
                    <img class="goldenKey" src="../assets/images/key.png" alt="Golden key" />
                </section>

            <?php endif; ?>
        </section>
    </main>

    <!-- Mastercode modal -->
    <!-- Modal -->
    <section id="mastercodeModal" class="mastercodeModal adminHidden">
        <article class="whiteBox dashboardBox mastercodeModalContent">
            <h3>Enter Mastercode</h3>
            <input type="password" id="mastercodeInput" placeholder="Mastercode">
            <div>
                <button id="submitMastercode" class="adminButton">Authorize</button>
                <button id="cancelMastercode" class="adminButton cancelButton">Cancel</button>
            </div>
        </article>
    </section>

    <script src="../assets/scripts/functions.js"></script>
    <script src="../assets/scripts/admin/dashboard.js"></script>
    <script src="../assets/scripts/admin/financials.js"></script>
    <script src="../assets/scripts/admin/users.js"></script>
</body>

</html>