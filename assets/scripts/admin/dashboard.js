// ---------- SWITCH DASHBOARD ----------
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

// ---------- MASTERCODE MODAL ----------
const submitMastercodeBtn = document.getElementById("submitMastercode");
const cancelMastercodeBtn = document.getElementById("cancelMastercode");

// Cancel-button -> closes modal
cancelMastercodeBtn.onclick = () => closeMastercodeModal();
