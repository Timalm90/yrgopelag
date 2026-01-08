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
const closeBtn = document.querySelector(".modalCloseFeature");
const secretBoxes = document.querySelectorAll(".secretBoxes");
const modalCategories = document.querySelectorAll(".modalCategory");

// Click on ? box
secretBoxes.forEach((icon) => {
  icon.addEventListener("click", () => {
    const categoryId = icon.dataset.categoryId;

    // Hide all categories
    modalCategories.forEach((cat) => (cat.style.display = "none"));

    // Show choosen category
    const selectedCat = featureModal.querySelector(
      `.modalCategory[data-category-id="${categoryId}"]`
    );
    if (selectedCat) selectedCat.style.display = "block";

    // Show modal
    featureModal.style.display = "block";
  });
});

// Close modal
closeBtn.onclick = () => {
  featureModal.style.display = "none";
};

// ESC
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") featureModal.style.display = "none";
});
