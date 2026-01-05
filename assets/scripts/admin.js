// // SWITCH DASHBOARD
// const dashboardButtons = document.querySelectorAll(".dashboardButton");
// const dashboards = document.querySelectorAll(".dashboard");

// function activateDashboard(index) {
//   dashboardButtons.forEach((button, i) => {
//     button.classList.toggle("active", i === index);
//   });

//   dashboards.forEach((dashboard, i) => {
//     dashboard.classList.toggle("adminHidden", i !== index);
//   });
// }

// // Default: Settings
// activateDashboard(0);

// dashboardButtons.forEach((button, index) => {
//   button.addEventListener("click", () => {
//     activateDashboard(index);
//   });
// });

// ---------- CHECK BALANCE ----------
const checkBalanceBtn = document.getElementById("checkBalanceBtn");
const balanceOutput = document.getElementById("balanceOutput");

checkBalanceBtn.addEventListener("click", () => {
  // Clear earlier messages
  balanceOutput.textContent = "";

  fetch("../app/admin/checkBalance.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.credit !== undefined) {
        balanceOutput.textContent = `Credit: ${data.credit}`;
      } else {
        balanceOutput.textContent = data.error || "Something went wrong";
      }
    })
    .catch(() => {
      balanceOutput.textContent = "Could not reach centralbank";
    });
});

// ---------- CHANGE PRICE ON ROOM OR FEATURE----------
const categorySelect = document.getElementById("category");
const itemSelect = document.getElementById("item");
const currentPriceEl = document.getElementById("currentPrice");

// All item options
const options = Array.from(itemSelect.options).slice(1);

// Show items for choosen category
categorySelect.addEventListener("change", () => {
  const selectedCategory = categorySelect.value;
  itemSelect.selectedIndex = 0; // reset
  options.forEach((option) => {
    option.hidden = option.dataset.category !== selectedCategory;
  });
  currentPriceEl.textContent = ""; // reset current price
});

// Show current price for choosen item
itemSelect.addEventListener("change", () => {
  const category = categorySelect.value;
  const item = itemSelect.value;

  if (!category || !item) {
    currentPriceEl.textContent = "";
    return;
  }

  fetch("../app/admin/getCurrentPrice.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ category, item }),
  })
    .then((res) => res.json())
    .then((data) => {
      currentPriceEl.textContent = data.success ? data.price : "Error";
    })
    .catch(() => {
      currentPriceEl.textContent = "Error";
    });
});
