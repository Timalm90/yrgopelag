// ----- SELECT ELEMENTS -----
const bookingForm = document.querySelector(".bookingForm form");
const totalPriceDisplay = document.querySelector("#totalPrice");
const bookingTypeRadios = document.querySelectorAll(
  'input[name="bookingType"]'
);

// ----- FUNCTIONS -----

// Reset total price to 0
function resetTotalPrice() {
  totalPriceDisplay.textContent = "0";
}

// Update total price dynamically
function updateTotalPrice() {
  const bookingType = document.querySelector(
    'input[name="bookingType"]:checked'
  ).value;

  const formData = new FormData(bookingForm);

  // If Day Pass: remove room, arrivalDate & departureDate
  if (bookingType === "day") {
    formData.delete("room");
    formData.delete("arrivalDate");
    formData.delete("departureDate");
  }

  fetch("app/totalprice.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      if (!res.ok) throw new Error("Network response was not ok");
      return res.json();
    })
    .then((data) => {
      // Error log
      if (data.errors && data.errors.length > 0)
        console.warn("Errors:", data.errors);
      // Update total price
      totalPriceDisplay.textContent = data.totalPrice ?? "0";
    })
    .catch((err) => {
      console.error("Total price fetch failed:", err);
      resetTotalPrice();
    });
}

// ----- EVENT LISTENERS -----

// Update price when user toggle between Book Room / Day pass
bookingTypeRadios.forEach((radio) => {
  radio.addEventListener("change", updateTotalPrice);
});

// Update price every time something is changed in form
bookingForm.addEventListener("change", (e) => {
  if (e.target.name === "bookingType") return; // handled above
  updateTotalPrice();
});

// Update totalprice when site is reloaded
updateTotalPrice();
