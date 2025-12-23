<?php

// ToDo:
// Lägg ett formulär som skapar nya admins

?>
<section>
    <h2>Create a new admin account</h2>
    <form action="/app/admin/createAdmin.php" method="post">
        <div>
            <label for="newUsername">New username:</label>
            <input type="text" name="newUsername" placeholder="Enter new username" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit">Create new admin</button>
    </form>
</section>