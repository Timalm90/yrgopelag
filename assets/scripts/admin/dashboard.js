// // SWITCH DASHBOARD
const dashboardButtons = document.querySelectorAll(".dashboardMenu button");
const dashboards = document.querySelectorAll(".dashboard");

dashboardButtons.forEach((button, index) => {
  button.addEventListener("click", () => {
    // Active button
    dashboardButtons.forEach((button, i) =>
      button.classList.toggle("active", i === index)
    );

    // Clear confirmation/error message
    clearAdminMessage();

    // Show selected dashboard
    dashboards.forEach((dashboard, i) => {
      dashboard.classList.toggle("adminHidden", i !== index);
    });
  });
});

// ----------- ADMIN MESSAGE ----------
// const adminMessageBox = document.querySelector("#adminMessageBox");

// function showAdminMessage(message, isError = false) {
//   adminMessageBox.textContent = message;

//   // Toggle visibility
//   adminMessageBox.classList.remove("adminHidden");

//   // Add class to message
//   if (isError) {
//     adminMessageBox.classList.add("adminError");
//   } else {
//     adminMessageBox.classList.add("adminConfirmation");
//   }
// }

// function clearAdminMessage() {
//   adminMessageBox.textContent = "";

//   // Change classes
//   adminMessageBox.classList.remove("adminError", "adminConfirmation");
//   adminMessageBox.classList.add("adminHidden");
// }

// ---------- MASTERCODE MODAL ----------
// const mastercodeModal = document.getElementById("mastercodeModal");
// const mastercodeInput = document.getElementById("mastercodeInput");
const submitMastercodeBtn = document.getElementById("submitMastercode");
const cancelMastercodeBtn = document.getElementById("cancelMastercode");

// Cancel-button -> closes modal
cancelMastercodeBtn.onclick = () => closeMastercodeModal();

// // Open modal
// function openMastercodeModal() {
//   mastercodeInput.value = "";
//   mastercodeModal.classList.remove("adminHidden");
//   mastercodeModal.classList.add("showModal");
// }

// // Close modal
// function closeMastercodeModal() {
//   mastercodeModal.classList.remove("showModal");
//   mastercodeModal.classList.add("adminHidden");
// }
