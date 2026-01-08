// ---------- CREATE ADMIN ACCOUNT ----------
const createAdminBtn = document.querySelector("#createAdmin");

createAdminBtn.addEventListener("click", () => {
  // Clear earlier messages
  clearAdminMessage();

  const username = document.querySelector("#newUsername").value;
  const password1 = document.querySelector("#newUserpassword1").value;
  const password2 = document.querySelector("#newUserpassword2").value;

  // Validate
  if (!username || !password1 || !password2) {
    showAdminMessage("Create admin: Please fill all fields", true);
    return;
  }

  // Open mastercode modal:
  openMastercodeModal();

  const authorizeHandler = () => {
    const mastercode = mastercodeInput.value;

    if (!mastercode) {
      showAdminMessage("Create admin: Mastercode is required", true);
      return;
    }

    fetch("../app/admin/createAdmin.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        newUsername: username,
        password1: password1,
        password2: password2,
        masterCode: mastercode,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showAdminMessage(data.message);

          // Clear fields
          document.querySelector("#newUsername").value = "";
          document.querySelector("#newUserpassword1").value = "";
          document.querySelector("#newUserpassword2").value = "";
          mastercodeInput.value = "";
        } else {
          showAdminMessage(
            data.error || "Create admin: Something went wrong",
            true
          );
        }
      })
      .catch(() => {
        showAdminMessage("Create admin: Could not reach server", true);
      });
    closeMastercodeModal();

    submitMastercodeBtn.removeEventListener("click", authorizeHandler);
  };
  submitMastercodeBtn.addEventListener("click", authorizeHandler);
});

// ---------- CHANGE PASSWORD ----------
const changePasswordBtn = document.querySelector("#changePasswordButton");

changePasswordBtn.addEventListener("click", (e) => {
  // Clear earlier messages
  clearAdminMessage();

  // Fetch values from input fields
  const username = document.querySelector("#usernameChangePassword").value;
  const currentPassword = document.querySelector("#currentPassword").value;
  const newPassword1 = document.querySelector("#changePassword1").value;
  const newPassword2 = document.querySelector("#changePassword2").value;

  // Validation
  if (!username || !currentPassword || !newPassword1 || !newPassword2) {
    showAdminMessage("Change password: Please fill all fields", true);
    return;
  }

  // Open modal
  openMastercodeModal();

  const authorizeHandler = () => {
    const mastercode = mastercodeInput.value;

    if (!mastercode) {
      showAdminMessage("Change password: Mastercode is required", true);
      return;
    }

    fetch("../app/admin/changePassword.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        usernameChangePassword: username,
        currentPassword: currentPassword,
        changePassword1: newPassword1,
        changePassword2: newPassword2,
        masterCode: mastercode,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showAdminMessage(data.message);

          // Clear fields
          document.querySelector("#usernameChangePassword").value = "";
          document.querySelector("#currentPassword").value = "";
          document.querySelector("#changePassword1").value = "";
          document.querySelector("#changePassword2").value = "";
          mastercodeInput.value = "";
        } else {
          showAdminMessage(
            data.error || "Change password: Something went wrong",
            true
          );
        }
      })
      .catch(() => {
        showAdminMessage("Change password: Could not reach server", true);
      });
    closeMastercodeModal();
    submitMastercodeBtn.removeEventListener("click", authorizeHandler);
  };
  submitMastercodeBtn.addEventListener("click", authorizeHandler);
});
