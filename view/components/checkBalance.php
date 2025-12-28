<section class="whiteBox">
    <h2>Check balance</h2>

    <form action="../app/admin/checkBalance.php" method="post">

        <button type="submit">Check!</button>
    </form>

    <?php if (isset($_SESSION['accountInfo'])): ?>
        <h3>Account status:</h3>
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