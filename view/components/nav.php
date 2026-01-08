<!-- DEPLOY -->
<!-- <nav>
    <img class="navLogo"
        src="/yrgopelag/assets/images/logo.png"
        alt="Logo Yoshi's Resort" />

    <div class="navbar">
        <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'activeNav' : ''; ?>"
            href="/yrgopelag/index.php">
            Home</a>

        <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'admin.php' ? 'activeNav' : ''; ?>"
            href="/yrgopelag/view/admin.php">Admin</a>

        <?php if (isset($_SESSION['admin'])) { ?>
            <a class="navLink"
                href="/yrgopelag/app/admin/logout.php">
                Logout
            </a>
        <?php } else { ?>
            <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'login.php' ? 'activeNav' : ''; ?>"
                href="/yrgopelag/view/login.php">Login</a>
        <?php } ?>
    </div>
</nav> -->

<!-- LOCALHOST -->
<nav>
    <img class="navLogo"
        src="/assets/images/logo.png"
        alt="Logo Yoshi's Resort" />

    <div class="navbar">
        <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'activeNav' : ''; ?>"
            href="/index.php">
            Home</a>

        <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'admin.php' ? 'activeNav' : ''; ?>"
            href="/view/admin.php">Admin</a>

        <?php if (isset($_SESSION['admin'])) { ?>
            <a class="navLink"
                href="<?= dirname($_SERVER['SCRIPT_NAME']) === '/view' ? '../app/admin/logout.php' : 'app/admin/logout.php'; ?>">
                Logout
            </a>
        <?php } else { ?>
            <a class="navLink <?= basename($_SERVER['SCRIPT_NAME']) === 'login.php' ? 'activeNav' : ''; ?>"
                href="/view/login.php">Login</a>
        <?php } ?>
    </div>
</nav>