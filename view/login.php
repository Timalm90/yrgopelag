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
    <link rel="stylesheet" href="../assets/styles/admin.css">
    <link rel="stylesheet" href="../assets/styles/nav.css">
</head>

<body>
    <?php
    require __DIR__ . "/nav.php";
    ?>
    <div class="adminBackground">
        <main>
            <article class="adminLoginForm">
                <h1>Login</h1>

                <form action="/app/admin/login.php" method="post">
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