const table = document.querySelector("#items-table");
const pagination = document.querySelector("#pagination");
const itemName = document.querySelector("#item-name");
const itemAmount = document.querySelector("#item-amount");
const deleteSheet = document.querySelector("#delete-sheet");
const editSheet = document.querySelector("#edit-sheet");
const ctx = document.getElementById("graph");
let chart = undefined;
let currentSheet = undefined;

async function fetchUsers() {
  try {
    const res = await fetch("../api/users.php");
    console.log(res);
    const data = await res.json();
    console.log(data);
    const table = document.querySelector("#usersTable");
    table.innerHTML = "";
    for (const row of data) {
      const tr = document.createElement("tr");
      const name = document.createElement("td");
      name.innerText = row.name;
      const type = document.createElement("td");
      type.innerText = row.type;
      tr.appendChild(name);
      tr.appendChild(type);
      tr.setAttribute("data-id", row.id);
      tr.id = `item-${row.id}`;
      table.appendChild(tr);
    }
  } catch (e) {
    console.error(e.toString());
  }
}

async function fetchSheets() {
  const result = await fetch("../api/sheets.php");
  const json = await result.json();
  if (!json.find((sheet) => sheet.id === currentSheet)) {
    currentSheet = json[0].id;
  }
  updatePagination(json);
  switchActiveSheet();
  bindListeners();
  fetchItems();
}

async function updatePagination(sheets) {
  pagination.innerHTML = ""; // Clear existing pagination

  sheets.forEach((sheet) => {
    const mItem = document.createElement("li");
    mItem.classList.add("page-item");
    mItem.innerHTML = `<a class="page-link" id="sheet-${sheet.id}" data-id="${sheet.id}">${sheet.name}</a>`;
    pagination.appendChild(mItem);
  });

  if (user_type !== "admin") return;

  // Admin-only: Add "+" button for creating new sheet
  const addItem = document.createElement("li");
  addItem.classList.add("page-item");
  addItem.innerHTML = `
      <a class="page-link" href="#" id="add-new-sheet">
        +
      </a>
      `;
  pagination.appendChild(addItem);

  // Set up event listener for adding new sheet
  const addSheet = document.querySelector("#add-new-sheet");
  addSheet.addEventListener("click", async (e) => {
    e.preventDefault();
    const newSheetName = prompt("Enter the name of the new sheet:");
    if (newSheetName && newSheetName.trim() !== "") {
      try {
        const res = await fetch("../api/sheets.php", {
          method: "POST",
          body: JSON.stringify({ name: newSheetName }),
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (res.status !== 201) {
          throw new Error("An error occurred while creating the new sheet");
        }

        const newSheet = await res.json();
        // Fetch the updated list of sheets
        currentSheet = parseInt(newSheet.id);
        fetchSheets();
      } catch (e) {
        console.error(e.toString());
      }
    }
  });
}

function switchActiveSheet() {
  // Change active link
  document.querySelectorAll(".page-link").forEach((link) => {
    if (link.getAttribute("data-id") == currentSheet)
      link.classList.add("active");
    else link.classList.remove("active");
  });
  fetchItems();
}

async function fetchItems() {
  // Get items for current sheet
  try {
    const res = await fetch(`../api/expenses.php?sheet_id=${currentSheet}`);
    const data = await res.json();
    const table = document.querySelector("#items-table");
    table.innerHTML = "";
    // Loop over each row
    for (const row of data) {
      const tr = document.createElement("tr");
      const item = document.createElement("td");
      item.innerText = row.purpose;
      const amount = document.createElement("td");
      amount.innerText = row.amount;
      const actions = document.createElement("td");
      actions.innerHTML = `
          <button class="btn btn-primary edit-item" data-id=${row.id} data-purpose="${row.purpose}" data-amount="${row.amount}">
            Edit
          </button>
          <button class="btn btn-danger delete-item" data-id=${row.id}>
            Delete
          </button>
          `;
      tr.appendChild(item);
      tr.appendChild(amount);
      tr.appendChild(actions);
      tr.setAttribute("data-id", row.id);
      tr.id = `item-${data.id}`;
      table.appendChild(tr);
    }

    document.querySelectorAll(".delete-item").forEach((btn) => {
      btn.removeEventListener("click", onDeleteItem);
      btn.addEventListener("click", onDeleteItem);
    });

    document.querySelectorAll(".edit-item").forEach((btn) => {
      btn.removeEventListener("click", onEditItem);
      btn.addEventListener("click", onEditItem);
    });
    drawGraph(data);
  } catch (e) {
    console.error(e.toString());
  }
}

function onDeleteItem(e) {
  {
    fetch("../api/expenses.php", {
      method: "DELETE",
      body: JSON.stringify({
        expense_id: e.target.getAttribute("data-id"),
      }),
    })
      .then((res) => {
        if (res.status != 204) throw "An error ocurred";
        fetchItems();
      })
      .catch((e) => console.error(e.toString()));
  }
}

function onEditItem(e) {
  const button = e.target;
  const id = button.getAttribute("data-id");
  const purpose = button.getAttribute("data-purpose");
  const amount = button.getAttribute("data-amount");

  // Set modal input values
  document.querySelector("#edit-item-id").value = id;
  document.querySelector("#edit-item-name").value = purpose;
  document.querySelector("#edit-item-amount").value = amount;

  // Show the modal using Bootstrap 5 native JavaScript
  const editItemModal = new bootstrap.Modal(
    document.getElementById("editItemModal"),
  );
  editItemModal.show();
}

document.querySelector("#edit-item-form").addEventListener("submit", (e) => {
  e.preventDefault();
  let formData = new FormData(e.target);
  fetch("../api/expenses.php", {
    method: "PUT",
    body: JSON.stringify(Object.fromEntries(formData)),
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((res) => {
      if (res.status != 200) throw "An error occurred";
      fetchItems();
      // Hide the modal after saving changes
      const editItemModal = bootstrap.Modal.getInstance(
        document.getElementById("editItemModal"),
      );
      editItemModal.hide();
    })
    .catch((e) => console.error(e.toString()));
});

function bindListeners() {
  document.querySelectorAll(".page-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      currentSheet = e.target.getAttribute("data-id");
      switchActiveSheet();
    });
  });

  document.querySelector("#add-item-form").addEventListener("submit", (e) => {
    e.preventDefault();
    let formData = new FormData(e.target);
    formData.append("sheet_id", currentSheet);
    e.target.reset();
    fetch("../api/expenses.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => {
        if (res.status != 201) throw "An error ocurred";
        fetchItems();
      })
      .catch((e) => console.error(e.toString()));
  });
}

if (user_type === "admin") {
  deleteSheet.addEventListener("click", async (e) => {
    e.preventDefault();
    const sheet_id = currentSheet;
    try {
      const res = await fetch("../api/sheets.php", {
        method: "DELETE",
        body: JSON.stringify({ id: sheet_id }),
        headers: {
          "Content-Type": "application/json",
        },
      });
      // Fetch the updated list of sheets
      fetchSheets();
    } catch (e) {
      console.error(e.toString());
    }
  });
  editSheet.addEventListener("click", async (e) => {
    e.preventDefault();
    const sheet_id = currentSheet;
    const newSheetName = prompt("Enter the new name of the sheet:");
    if (newSheetName && newSheetName.trim() !== "") {
      try {
        const res = await fetch("../api/sheets.php", {
          method: "PUT",
          body: JSON.stringify({ id: sheet_id, name: newSheetName }),
          headers: {
            "Content-Type": "application/json",
          },
        });
        // Fetch the updated list of sheets
        fetchSheets();
      } catch (e) {
        console.error(e.toString());
      }
    }
  });
}

function drawGraph(data) {
  const labels = data.map((item) => item.purpose);
  const amounts = data.map((item) => item.amount);
  // Draw Pie Chart of expenses
  chart && chart.destroy();
  chart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Expenses",
          data: amounts,
          backgroundColor: [
            "rgba(255, 99, 132, 0.2)",
            "rgba(54, 162, 235, 0.2)",
            "rgba(255, 206, 86, 0.2)",
            "rgba(75, 192, 192, 0.2)",
            "rgba(153, 102, 255, 0.2)",
          ],
          borderColor: [
            "rgba(255, 99, 132, 1)",
            "rgba(54, 162, 235, 1)",
            "rgba(255, 206, 86, 1)",
            "rgba(75, 192, 192, 1)",
            "rgba(153, 102, 255, 1)",
          ],
          borderWidth: 1,
        },
      ],
    },
  });
}

fetchSheets();
