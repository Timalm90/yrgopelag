const categorySelect = document.getElementById("category");
const itemSelect = document.getElementById("item");

// All options in form
const options = Array.from(itemSelect.options).slice(1);

categorySelect.addEventListener("change", () => {
  const selectedCategory = categorySelect.value;

  // Empty when changed category
  itemSelect.selectedIndex = 0;

  // Show only item options belong to choosen category
  options.forEach((option) => {
    if (option.dataset.category === selectedCategory) {
      option.hidden = false;
    } else {
      option.hidden = true;
    }
  });
});

// ADD ON: show current price
const currentPriceEl = document.getElementById("currentPrice");

itemSelect.addEventListener("change", () => {
  const category = categorySelect.value;
  const item = itemSelect.value;

  if (!category || !item) {
    currentPriceEl.textContent = "—";
    return;
  }

  fetch("../app/admin/getCurrentPrice.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      category: category,
      item: item,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        currentPriceEl.textContent = data.price;
      } else {
        currentPriceEl.textContent = "Error";
      }
    })
    .catch(() => {
      currentPriceEl.textContent = "Error";
    });
});

// --------------------------------------------------------------------------------------
//VERSION 2 MED DYNAMISKT PRIS I REALTID
// const categorySelect = document.getElementById("category");
// const itemSelect = document.getElementById("item");
// const currentPriceEl = document.getElementById("currentPrice");

// // När item ändras → hämta pris
// itemSelect.addEventListener("change", function () {
//   const category = categorySelect.value;
//   const item = this.value;

//   if (!category || !item) {
//     currentPriceEl.textContent = "–";
//     return;
//   }

//   fetch(`/app/admin/getCurrentPrice.php?category=${category}&item=${item}`)
//     .then((response) => response.json())
//     .then((data) => {
//       if (data.success) {
//         currentPriceEl.textContent = data.price + " SEK";
//       } else {
//         currentPriceEl.textContent = "Not found";
//       }
//     })
//     .catch(() => {
//       currentPriceEl.textContent = "Error";
//     });
// });
