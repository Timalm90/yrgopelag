<article class="dashboardBox">
    <h3>Create a new admin account</h3>
    <div>
        <label for="newUsername">New username:</label>
        <input type="text" id="newUsername" name="newUsername" placeholder="Enter new username" required>
    </div>

    <div>
        <label for="password1">Password:</label>
        <input type="password" id="newUserpassword1" name="password1" placeholder="Enter password" required>
    </div>

    <div>
        <label for="password2">Repeat password:</label>
        <input type="password" id="newUserpassword2" name="password2" placeholder="Enter password again" required>
    </div>

    <button id="createAdmin" class="adminButton requireMastercode">Create new admin</button>
</article>