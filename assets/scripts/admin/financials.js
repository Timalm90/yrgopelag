// ---------- CHECK BALANCE ----------
const checkBalanceBtn = document.querySelector("#checkBalanceBtn");
const balanceOutput = document.querySelector("#balanceOutput");

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

// ---------- CHANGE PRICE ON ROOM OR FEATURE: TOGGLE FORM ----------
const categorySelect = document.getElementById("category");
const itemSelect = document.getElementById("item");
const currentPriceEl = document.getElementById("currentPrice");

// All item options except first
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

// ---------- CHANGE PRICE ON ROOM OR FEATURE / PRESENT RESULT ----------
const changePriceBtn = document.getElementById("changePriceBtn");
const priceInput = document.getElementById("priceInput");

changePriceBtn.addEventListener("click", () => {
  clearAdminMessage();

  const category = categorySelect.value;
  const item = itemSelect.value;
  const price = priceInput.value;

  // Validate
  if (!category || !item || !price) {
    showAdminMessage("Please fill all fields", true);
    return;
  }

  // Open mastercode modal
  openMastercodeModal();

  const authorizeHandler = () => {
    const mastercode = mastercodeInput.value;

    if (!mastercode) {
      showAdminMessage("Change price: Mastercode is required", true);
      return;
    }

    fetch("../app/admin/changePrice.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        category,
        item,
        price,
        masterCode: mastercode,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showAdminMessage(data.message);

          // Clear form
          categorySelect.selectedIndex = 0;
          itemSelect.selectedIndex = 0;
          priceInput.value = "";
          currentPriceEl.textContent = "";
          mastercodeInput.value = "";
        } else {
          showAdminMessage(
            data.error || "Change price: Something went wrong",
            true
          );
        }
      })
      .catch(() => {
        showAdminMessage("Change price: Could not reach server", true);
      });

    closeMastercodeModal();
    submitMastercodeBtn.removeEventListener("click", authorizeHandler);
  };

  submitMastercodeBtn.addEventListener("click", authorizeHandler);
});

// ---------- ADD FEATURE ----------
const addFeatureBtn = document.querySelector(".addFeatureButton");
const featureSelect = document.querySelector(".featureSelect");

// Click event
addFeatureBtn.addEventListener("click", (e) => {
  // Clear ealier messages
  clearAdminMessage();

  const selectedFeature = featureSelect.value;

  // Validation
  if (!selectedFeature) {
    showAdminMessage("Add feature: Please select a feature to add", true);
    return;
  }

  // Open mastercode modal
  openMastercodeModal();

  const authorizeHandler = () => {
    const mastercode = mastercodeInput.value;

    if (!mastercode) {
      showAdminMessage("Add feature: Mastercode is required", true);
      return;
    }

    fetch("../app/admin/addFeatures.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        item: selectedFeature,
        masterCode: mastercode,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showAdminMessage(data.message);

          // Clear field
          featureSelect.selectedIndex = 0;
          mastercodeInput.value = "";
        } else {
          showAdminMessage(
            data.error || "Add feature: Something went wrong",
            true
          );
        }
      })
      .catch(() => {
        showAdminMessage("Add feature: Could not reach server", true);
      });

    closeMastercodeModal();
    submitMastercodeBtn.removeEventListener("click", authorizeHandler);
  };
  submitMastercodeBtn.addEventListener("click", authorizeHandler);
});
