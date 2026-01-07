// ---------------------------------------- INDEX PAGE ----------------------------------------
// Clear selection in calendar
function clearSelection() {
  document.querySelectorAll(".day.selected").forEach((d) => {
    d.classList.remove("selected");
  });
}

// Update form & price when toggle Book Room/Day pass
function updateForm() {
  const bookRoomRadio = document.querySelector("#bookRoom");
  const dayPassRadio = document.querySelector("#dayPass");
  const togglableElements = document.querySelectorAll(".togglable");
  const arrivalLabel = document.querySelector(".arrivalLabel");
  const legendRoom = document.querySelector(".legendRoom");

  if (dayPassRadio.checked) {
    togglableElements.forEach((el) => el.classList.add("hidden"));
    arrivalLabel.textContent = "Date:";
    legendRoom.textContent = "Choose Date";
  } else {
    togglableElements.forEach((el) => el.classList.remove("hidden"));
    arrivalLabel.textContent = "Arrival:";
    legendRoom.textContent = "Choose Room and Date";
  }

  // Update totalprice if function exists
  if (typeof updateTotalPrice === "function") updateTotalPrice();
}

// Check dates in form
function checkDates() {
  const arrivalInput = document.querySelector('input[name="arrivalDate"]');
  const departureInput = document.querySelector('input[name="departureDate"]');
  const departureBox = document.querySelector(".departureBox");
  const dateHint = departureBox.querySelector(".departureHint");

  const arrival = new Date(arrivalInput.value);
  const departure = new Date(departureInput.value);

  dateHint.textContent = "";
  dateHint.classList.remove("visible");

  if (arrivalInput.value && departureInput.value && arrival > departure) {
    dateHint.textContent = "Arrival date cannot be later than departure date.";
    dateHint.classList.add("visible");
  }
}

// Update total price
function resetTotalPrice() {
  const totalPriceDisplay = document.querySelector("#totalPrice");
  totalPriceDisplay.textContent = "0";
}

function updateTotalPrice() {
  const bookingForm = document.querySelector(".bookingForm form");
  const totalPriceDisplay = document.querySelector("#totalPrice");
  const bookingType = document.querySelector(
    'input[name="bookingType"]:checked'
  ).value;

  const formData = new FormData(bookingForm);

  if (bookingType === "day") {
    formData.delete("room");
    formData.delete("arrivalDate");
    formData.delete("departureDate");
  }

  fetch("app/totalprice.php", { method: "POST", body: formData })
    .then((res) => res.json())
    .then((data) => {
      if (data.errors && data.errors.length > 0)
        console.warn("Errors:", data.errors);
      totalPriceDisplay.textContent = data.totalPrice ?? "0";
    })
    .catch((err) => {
      console.error("Total price fetch failed:", err);
      resetTotalPrice();
    });
}

// ---------------------------------------- ADMIN PAGE ----------------------------------------
// Show message
function showAdminMessage(message, isError = false) {
  const box = document.querySelector("#adminMessageBox");
  box.textContent = message;
  box.classList.remove("adminHidden", "adminError", "adminConfirmation");
  box.classList.add(isError ? "adminError" : "adminConfirmation");
}

// Clear message
function clearAdminMessage() {
  const box = document.querySelector("#adminMessageBox");
  box.textContent = "";
  box.classList.remove("adminError", "adminConfirmation");
  box.classList.add("adminHidden");
}

// Open modal
function openMastercodeModal() {
  const modal = document.getElementById("mastercodeModal");
  const input = document.getElementById("mastercodeInput");
  input.value = "";
  modal.classList.remove("adminHidden");
  modal.classList.add("showModal");
}

// Close modal
function closeMastercodeModal() {
  const modal = document.getElementById("mastercodeModal");
  modal.classList.remove("showModal");
  modal.classList.add("adminHidden");
}
