<?php

// Check bank account, use your API-key

?>
<section class="whiteBox">
    <h2>Check account</h2>

    <form action="../app/admin/checkAccount.php" method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter username" required>
        </div>

        <div>
            <label for="apiKey">API-key:</label>
            <input type="password" name="apiKey" placeholder="Enter your API-key" required>
        </div>

        <button type="submit">Check!</button>
    </form>

    <?php if (isset($_SESSION['accountInfo'])): ?>
        <h3>Account status:</h3>
        <p><strong>User:</strong> <?= htmlspecialchars($_SESSION['accountInfo']['user']) ?></p>
        <p><strong>Credit:</strong> <?= htmlspecialchars($_SESSION['accountInfo']['credit']) ?></p>
    <?php $_SESSION['accountInfo'] = NULL;
    endif;

    if (!empty($_SESSION['adminErrors'])): ?>
        <ul>
            <?php foreach ($_SESSION['adminErrors'] as $error): ?>
                <li>
                    <?= htmlspecialchars($error) ?>
                </li>
            <?php endforeach ?>
        </ul>

    <?php
        $_SESSION['adminErrors'] = NULL;
    endif ?>
</section>