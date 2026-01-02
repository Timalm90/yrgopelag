// Booking confirmation modal
const confirmationModal = document.querySelector(".modal");

if (confirmationModal) {
  // Close button
  const closeButton = modal.querySelector(".modalClose");
  if (closeButton) {
    closeButton.addEventListener("click", () => modal.remove());
  }

  // ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") modal.remove();
  });
}

// Feature description modal
const modal = document.querySelector("#featureModal");
const openBtn = document.querySelector(".secretBox");
const closeBtn = document.querySelector(".modalClose");

openBtn.onclick = () => {
  modal.style.display = "block";
};
closeBtn.onclick = () => {
  modal.style.display = "none";
};
window.onclick = (e) => {
  if (e.target == modal) modal.style.display = "none";
};
