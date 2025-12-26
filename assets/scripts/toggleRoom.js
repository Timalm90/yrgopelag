const buttons = document.querySelectorAll(".roomToggleBtn");
const sections = document.querySelectorAll(".roomSection");

function activateRoom(index) {
  buttons.forEach((btn, i) => {
    btn.classList.toggle("active", i === index);
    btn.setAttribute("aria-selected", i === index);
  });

  sections.forEach((section, i) => {
    section.classList.toggle("active", i === index);
  });
}

// Default: Standard (index 1)
activateRoom(1);

buttons.forEach((btn) => {
  btn.addEventListener("click", () => {
    activateRoom(Number(btn.dataset.room));
  });
});
