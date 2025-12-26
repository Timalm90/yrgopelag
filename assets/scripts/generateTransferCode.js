const getButton = document.getElementById("getTransferCode");

getButton.addEventListener("click", () => {
  const name = document.getElementById("name").value;
  const apiKey = document.getElementById("apiKey").value;

  // Create a container for error hints
  let transferError = document.getElementById("transferError");
  if (!transferError) {
    transferError = document.createElement("div");
    transferError.id = "transferError";
    transferError.className = "hint"; // Same styling as arrival > departure
    getButton.insertAdjacentElement("afterend", transferError);
  }

  // --- Remove if error ---
  transferError.textContent = "";
  transferError.classList.remove("visible");

  if (!name || !apiKey) {
    transferError.textContent = "Please fill in your name and API key";
    transferError.classList.add("visible"); // Show with CSS
    return;
  }

  // Fetch total price från frontend
  const totalCost =
    parseInt(document.querySelector("#totalPrice").textContent) || 0;

  if (totalCost <= 0) {
    transferError.textContent =
      "Total price is 0, please choose room and/or features to complete your order and try again!";
    transferError.classList.add("visible");
    return;
  }

  // --- Send request to backend ---
  fetch("/app/getTransferCode.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, apiKey, amount: totalCost }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.transferCode) {
        document.getElementById("transferCode").value = data.transferCode;
      } else {
        transferError.textContent = data.error;
        transferError.classList.add("visible");
      }
    })
    .catch((err) => {
      // console.error(err);
      transferError.textContent = "Failed to get transfer code";
      transferError.classList.add("visible");
    });
});
