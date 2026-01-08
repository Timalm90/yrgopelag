// ----- SELECT ELEMENTS -----
const bookRoomRadio = document.querySelector("#bookRoom");
const dayPassRadio = document.querySelector("#dayPass");
// const togglableElements = document.querySelectorAll(".togglable");
// const arrivalLabel = document.querySelector(".arrivalLabel");
// const legendRoom = document.querySelector(".legendRoom");

// ----- FUNCTIONS -----

// Toggle form fields when switching between Book Room / Day Pass
// function updateForm() {
//   if (dayPassRadio.checked) {
//     // If day pass: Hide room & departure
//     togglableElements.forEach((el) => el.classList.add("hidden"));
//     arrivalLabel.textContent = "Date:";
//     legendRoom.textContent = "Choose Date";
//   } else {
//     // If Book Room: Show all input fields
//     togglableElements.forEach((el) => el.classList.remove("hidden"));
//     arrivalLabel.textContent = "Arrival:";
//     legendRoom.textContent = "Choose Room and Date";
//   }

//   // Update totalprice when user toggle Book room / Day pass
//   if (typeof updateTotalPrice === "function") {
//     updateTotalPrice();
//   }
// }

// HINT TO USER WHEN ARRIVAL > DEPARTURE
const arrivalInput = document.querySelector('input[name="arrivalDate"]');
const departureInput = document.querySelector('input[name="departureDate"]');
const departureBox = document.querySelector(".departureBox");

// Create hint element
const dateHint = document.createElement("div");
dateHint.className = "hint departureHint";
departureBox.appendChild(dateHint);

// ----- EVENT LISTENERS -----
bookRoomRadio.addEventListener("change", updateForm);
dayPassRadio.addEventListener("change", updateForm);

// When site is reloaded
updateForm();

// function checkDates() {
//   const arrival = new Date(arrivalInput.value);
//   const departure = new Date(departureInput.value);

//   // Reset error
//   dateHint.textContent = "";
//   dateHint.classList.remove("visible");

//   if (arrivalInput.value && departureInput.value && arrival > departure) {
//     dateHint.textContent = "Arrival date cannot be later than departure date.";
//     dateHint.classList.add("visible");
//   }
// }

// When changes are made in form:
arrivalInput.addEventListener("change", checkDates);
departureInput.addEventListener("change", checkDates);
