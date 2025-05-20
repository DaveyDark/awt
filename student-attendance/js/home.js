function showAlert(message, type) {
  const alertPlaceholder = document.querySelector("#alert-placeholder");

  // Clear any previous alerts
  alertPlaceholder.innerHTML = "";

  // Create the new alert
  const alert = document.createElement("div");
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.role = "alert";
  alert.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  // Append the alert to the placeholder
  alertPlaceholder.appendChild(alert);
}

async function onScanSuccess(decodedText, decodedResult) {
  console.log(`Code matched = ${decodedText}`, decodedResult);

  try {
    // Send the decoded URN (decodedText) to the server for validation and marking attendance
    const response = await fetch("../api/validate_urn.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ urn: decodedText }),
    });

    const result = await response.json();

    if (response.ok) {
      // If the URN is valid, show success alert and mark the student as present
      showAlert(`Student ${result.name} marked as present!`, "success");
    } else {
      // If the URN is invalid, show an error alert
      showAlert(result.error, "danger");
    }
  } catch (error) {
    console.error("Error validating URN:", error);
    showAlert("An error occurred while processing the QR code.", "danger");
  }
}

function onScanFailure(error) {
  console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: { width: 250, height: 250 } },
  false,
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
