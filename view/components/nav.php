<nav>
    <img class="navLogo" src="assets/images/logo.png" alt="Logo Yoshi's Resort" />
    <div class="navbar">
        <a class="navLink <?php echo $_SERVER['SCRIPT_NAME'] === '/index.php' ? 'activeNav' : ''; ?>" href="index.php">Home</a>

        <a class="navLink <?php echo $_SERVER['SCRIPT_NAME'] === '/view/admin.php' ? 'activeNav' : ''; ?>" href="view/admin.php">Admin</a>

        <?php if (isset($_SESSION['admin'])) { ?>
            <a class="navLink" href="app/admin/logout.php">Logout</a>
        <?php } else { ?>
            <a class="navLink <?php echo $_SERVER['SCRIPT_NAME'] === '/view/login.php' ? 'activeNav' : ''; ?>" href="view/login.php">Login</a>
        <?php } ?>
        </ul>
    </div>
</nav>