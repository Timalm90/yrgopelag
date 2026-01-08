const buttons = document.querySelectorAll(".roomToggleBtn");
const sections = document.querySelectorAll(".roomSection");
const characters = document.querySelectorAll(".roomCharacter");

function activateRoom(index) {
  buttons.forEach((btn, i) => {
    btn.classList.toggle("active", i === index);
  });

  sections.forEach((section, i) => {
    section.classList.toggle("active", i === index);
  });

  characters.forEach((character, charIndex) => {
    character.classList.toggle("showCharacter", charIndex === index);
    character.classList.toggle("hiddenCharacter", charIndex !== index);
  });
}

// Default: Standard (index 1)
activateRoom(1);

buttons.forEach((btn) => {
  btn.addEventListener("click", () => {
    activateRoom(Number(btn.dataset.room));
  });
});
