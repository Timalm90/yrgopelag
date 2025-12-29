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

<!-- In deploy:
 img logo src = /MAPP/assets/images/logo.png
 Home href = /MAPP/index.php
 Admin href = /MAPP/view/admin.php
 Logout href = /MAPP/app/admin/logout.php
 Login href = /toadtest2/view/login.php

 2025-12-29 Added basename() for activeNav to work in deploy? Works!
        -->