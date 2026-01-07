// Booking confirmation modal
const confirmationModal = document.querySelector(".confirmationModal");

if (confirmationModal) {
  // Close button
  const closeButton = confirmationModal.querySelector(".modalClose");
  if (closeButton) {
    closeButton.addEventListener("click", () => confirmationModal.remove());
  }

  // ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") confirmationModal.remove();
  });
}

// Feature description modal
const featureModal = document.querySelector("#featureModal");
const openBtn = document.querySelector(".secretBox");
const closeBtn = document.querySelector(".modalCloseFeature");

openBtn.onclick = () => {
  featureModal.style.display = "block";
};
closeBtn.onclick = () => {
  featureModal.style.display = "none";
};
window.onclick = (e) => {
  if (e.target == featureModal) featureModal.style.display = "none";
};
