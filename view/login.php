<?php
require __DIR__ . "/../app/autoload.php";
require __DIR__ . "/../app/config.php";
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
    <div class="adminBackground">
        <main>
            <article class="adminLoginForm">
                <h1>Login</h1>

                <form action="../app/admin/login.php" method="post">
                    <div>
                        <label for="username">Username:</label>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </div>

                    <div>
                        <label for="password">Password:</label>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>

                    <button type="submit">Login</button>
                </form>
            </article>
        </main>
    </div>
</body>

</html>