<article class="dashboardBox changePassword">
    <h3>Change password</h3>
    <div class="layout">
        <label for="usernameChangePassword">Username:</label>
        <input type="text" id="usernameChangePassword" name="usernameChangePassword" placeholder="Enter new username" required>

        <label for="currentPassword">Current password:</label>
        <input type="password" id="currentPassword" name="currentPassword" placeholder="Enter password" required>

        <label for="changePassword1">New password:</label>
        <input type="password" id="changePassword1" name="changePassword1" placeholder="Enter password" required>

        <label for="changePassword2">Repeat new password:</label>
        <input type="password" id="changePassword2" name="changePassword2" placeholder="Enter password again" required>
    </div>

    <button id="changePasswordButton" class="adminButton requireMastercode">Change password</button>
</article>