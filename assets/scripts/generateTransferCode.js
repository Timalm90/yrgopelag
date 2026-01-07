const getButton = document.querySelector("#getTransferCode");
const errorMessage = document.querySelector("#transferCodeError");

getButton.addEventListener("click", () => {
  const name = document.querySelector("#name").value;
  const apiKey = document.querySelector("#apiKey").value;

  //Reset error
  errorMessage.textContent = "";
  errorMessage.classList.remove("hint");

  // Validate name & apiKey
  if (!name || !apiKey) {
    errorMessage.textContent = "Please fill in your name and API key";
    errorMessage.classList.add("hint"); // Show with CSS
    return;
  }

  // Fetch total price from frontend
  const totalCost =
    parseInt(document.querySelector("#totalPrice").textContent) || 0;

  if (totalCost <= 0) {
    errorMessage.textContent =
      "Total price is 0, please choose room and/or features to complete your order and try again!";
    errorMessage.classList.add("visible", "hint");
    return;
  }

  // --- Send request to backend ---
  fetch("app/getTransferCode.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, apiKey, amount: totalCost }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.transferCode) {
        document.querySelector("#transferCode").value = data.transferCode;
      } else {
        errorMessage.textContent = data.error;
        errorMessage.classList.add("hint");
      }
    })
    .catch((err) => {
      // console.error(err);
      errorMessage.textContent = "Failed to get transfer code";
      errorMessage.classList.add("hint");
    });
});
