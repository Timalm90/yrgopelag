// Fetch modal
const modal = document.querySelector(".modal");

if (modal) {
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
