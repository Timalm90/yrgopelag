let selectedArrivalDay = null;
let selectedDepartureDay = null;

// Clear selected days
function clearSelection() {
  document.querySelectorAll(".day.selected").forEach((d) => {
    d.classList.remove("selected");
  });
}

// Event listener on calendar
document.querySelectorAll(".roomWrapper .day").forEach((day) => {
  day.addEventListener("click", () => {
    // User can't choose booked dates
    if (day.classList.contains("booked")) return;

    const roomId = day.dataset.room;
    const date = day.dataset.date;
    const clickedDate = new Date(date);

    const arrivalInput = document.querySelector("#arrivalDate");
    const departureInput = document.querySelector("#departureDate");

    // Select room in form
    const roomInput = document.querySelector(`#room_${roomId}`);
    if (roomInput) roomInput.checked = true;

    // 1st click = arrival. If arrival > departure -> new arrival.
    if (!arrivalInput.value || clickedDate < new Date(arrivalInput.value)) {
      arrivalInput.value = date;
      if (departureInput) departureInput.value = "";
      selectedArrivalDay = day;
      selectedDepartureDay = null;

      clearSelection();
      day.classList.add("selected");
      return;
    }

    // If arrival is set, next click -> departure
    const arrivalDate = new Date(arrivalInput.value);
    if (clickedDate > arrivalDate) {
      departureInput.value = date;
      selectedDepartureDay = day;

      clearSelection();

      // Highlight choosen dates
      selectedArrivalDay.classList.add("selected");
      selectedDepartureDay.classList.add("selected");

      // Update dynamic total price (without eventListener change)
      updateTotalPrice();

      // Scroll to form
      const form = document.querySelector(".bookingForm");
      if (form) form.scrollIntoView({ behavior: "smooth" });
    }
  });
});
