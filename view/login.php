<?php
require __DIR__ . "/../app/autoload.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Yrgopelag</title>
    <link rel="stylesheet" href="../assets/styles/general.css">
    <link rel="stylesheet" href="../assets/styles/admin/admin.css">
    <link rel="stylesheet" href="../assets/styles/nav.css">
    <link rel="stylesheet" href="../assets/styles/admin/login.css">
</head>

<body>
    <?php
    require __DIR__ . "/components/nav.php";
    ?>

    <div class="adminBackground"></div>
    <main>
        <article class="whiteBox adminLoginForm">
            <h1>Login</h1>
            <section class="adminMessage">
                <?php if (isset($_SESSION['loginError'])): ?>
                    <div class="adminMessageBox adminError">

                        <?= ($_SESSION['loginError']);
                        $_SESSION['loginError'] = NULL; ?>
                    </div>
                <?php endif; ?>
            </section>

            <form action="../app/admin/login.php" method="post">
                <div class="loginLayout">
                    <label for="username">Username:</label>
                    <input type="text" name="username" placeholder="Enter username" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="centerButton">
                    <button class="adminButton" type="submit">Login</button>
                </div>
            </form>
        </article>
    </main>
</body>

</html>