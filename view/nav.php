<nav>
    <div class="navbar">
        <a class="navLink <?php echo $_SERVER['SCRIPT_NAME'] === '/index.php' ? 'activeNav' : ''; ?>" href="/index.php">Home</a>

        <a class="navLink <?php echo $_SERVER['SCRIPT_NAME'] === '/view/admin.php' ? 'activeNav' : ''; ?>" href="/view/admin.php">Admin</a>

        <?php if (isset($_SESSION['user'])) { ?>
            <a class="navLink" href="/app/admin/logout.php">Logout</a>
        <?php } else { ?>
            <a class="navLink" href="/view/admin.php">Login</a>
        <?php } ?>
        </ul>
    </div>
</nav>