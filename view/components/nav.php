<nav>
    <img class="navLogo"
        src="/toadtest/assets/images/logo.png"
        alt="Logo Yoshi's Resort" />

    <div class="navbar">
        <a class="navLink <?= $_SERVER['SCRIPT_NAME'] === '/index.php' ? 'activeNav' : ''; ?>"
            href="index.php">
            Home</a>

        <a class="navLink <?= $_SERVER['SCRIPT_NAME'] === '/view/admin.php' ? 'activeNav' : ''; ?>"
            href="view/admin.php">Admin</a>

        <?php if (isset($_SESSION['admin'])) { ?>
            <a class="navLink"
                href="<?= dirname($_SERVER['SCRIPT_NAME']) === '/view' ? '../app/admin/logout.php' : 'app/admin/logout.php'; ?>">
                Logout
            </a>
        <?php } else { ?>
            <a class="navLink <?= $_SERVER['SCRIPT_NAME'] === '/view/login.php' ? 'activeNav' : ''; ?>"
                href="view/login.php">Login</a>
        <?php } ?>
    </div>
</nav>

<!-- In deploy:
 href = /toadtest/index.php", /toadtest/view/admin.php, /toadtest/view/login.php
 
 In localhost: index.php-->