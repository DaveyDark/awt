const form = document.getElementById("arrangementForm");
const arrangementIdField = document.getElementById("arrangementId");
const nameField = document.getElementById("name");
const rowCountField = document.getElementById("rowCount");
const columnCountField = document.getElementById("columnCount");
const cancelEditButton = document.getElementById("cancelEdit");
const submitButton = form.querySelector('button[type="submit"]'); // Get the submit button
const arrangementsList = document.getElementById("arrangementsList");
const apiUrl = "../api/arrangements.php";

// Reset the form on page load to ensure no saved values from a previous session
resetForm();

// Fetch and display arrangements
function loadArrangements() {
  fetch(apiUrl)
    .then((response) => response.json())
    .then((data) => {
      arrangementsList.innerHTML = "";
      data.forEach((arrangement) => {
        arrangementsList.innerHTML += `
                        <tr>
                            <td>${arrangement.id}</td>
                            <td>${arrangement.name}</td>
                            <td>${arrangement.row_count}</td>
                            <td>${arrangement.column_count}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editArrangement(${arrangement.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteArrangement(${arrangement.id})">Delete</button>
                            </td>
                        </tr>
                    `;
      });
    });
}

// Create or Update an arrangement
form.addEventListener("submit", (e) => {
  e.preventDefault();
  const id = arrangementIdField.value;
  const name = nameField.value;
  const rowCount = rowCountField.value;
  const columnCount = columnCountField.value;

  if (id) {
    // Update - send data as JSON for PUT request
    fetch(`${apiUrl}?id=${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
        name: name,
        row_count: rowCount,
        column_count: columnCount,
      }),
    })
      .then((response) => response.json())
      .then(() => {
        resetForm();
        loadArrangements();
      });
  } else {
    // Create - send form data as FormData for POST request
    const formData = new FormData();
    formData.append("name", name);
    formData.append("row_count", rowCount);
    formData.append("column_count", columnCount);

    fetch(apiUrl, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then(() => {
        resetForm();
        loadArrangements();
      });
  }
});

// Edit an arrangement
window.editArrangement = (id) => {
  fetch(`${apiUrl}?id=${id}`)
    .then((response) => response.json())
    .then((arrangement) => {
      arrangementIdField.value = arrangement.id;
      nameField.value = arrangement.name;
      rowCountField.value = arrangement.row_count;
      columnCountField.value = arrangement.column_count;

      // Disable row and column fields when editing
      rowCountField.disabled = true;
      columnCountField.disabled = true;

      // Show cancel button and change submit button text to "Save Changes"
      cancelEditButton.style.display = "inline-block";
      submitButton.textContent = "Save Changes";
    });
};

// Delete an arrangement
window.deleteArrangement = (id) => {
  if (confirm("Are you sure you want to delete this arrangement?")) {
    fetch(apiUrl, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id }),
    })
      .then((response) => response.json())
      .then(() => loadArrangements());
  }
};

// Reset form and cancel edit mode
function resetForm() {
  form.reset();
  arrangementIdField.value = "";

  // Enable row and column fields when creating a new arrangement
  rowCountField.disabled = false;
  columnCountField.disabled = false;

  // Hide cancel button and change submit button text to "Create"
  cancelEditButton.style.display = "none";
  submitButton.textContent = "Create";
}

cancelEditButton.addEventListener("click", () => {
  resetForm();
});

// Initial load of arrangements
loadArrangements();
document.addEventListener("DOMContentLoaded", () => {
  resetForm();
});
