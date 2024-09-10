const usersTable = document.getElementById("usersTable");
const registerForm = document.getElementById("registerForm");
const apiUrl = "../api/users.php";
const editModal = new bootstrap.Modal(document.getElementById("editModal"));
const editForm = document.getElementById("editForm");
let currentUserId = null; // Store the ID of the user being edited

// Fetch and display the list of users
async function loadUsers() {
  try {
    const response = await fetch(apiUrl, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) throw new Error("Failed to fetch users");

    const users = await response.json();
    usersTable.innerHTML = "";

    users.forEach((user) => {
      const userRow = document.createElement("tr");

      userRow.innerHTML = `
          <td>${user.name}</td>
          <td>User</td>
          <td>
            <button class="btn btn-sm btn-warning edit-btn" data-id="${user.id}" data-name="${user.name}">Edit</button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${user.id}">Delete</button>
          </td>
        `;

      usersTable.appendChild(userRow);
    });

    // Attach event listeners to the edit and delete buttons
    document
      .querySelectorAll(".edit-btn")
      .forEach((button) => button.addEventListener("click", handleEdit));
    document
      .querySelectorAll(".delete-btn")
      .forEach((button) => button.addEventListener("click", handleDelete));
  } catch (error) {
    console.error("Error loading users:", error);
  }
}

// Register a new user
registerForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const name = document.getElementById("name").value;
  const password = document.getElementById("password").value;

  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ name, password }),
    });

    if (!response.ok) throw new Error("Failed to register user");

    // Reload the users list after registration
    loadUsers();
    registerForm.reset();
  } catch (error) {
    console.error("Error registering user:", error);
  }
});

// Handle user edit
async function handleEdit(event) {
  currentUserId = event.target.getAttribute("data-id");
  const userName = event.target.getAttribute("data-name");

  // Set the form fields with current user values
  document.getElementById("edit-name").value = userName;

  // Show the modal
  editModal.show();
}

// Handle form submission for editing a user
editForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const newUserName = document.getElementById("edit-name").value;
  const newPassword = document.getElementById("edit-password").value;

  if (newUserName && newPassword) {
    try {
      const response = await fetch(apiUrl, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: currentUserId,
          name: newUserName,
          password: newPassword,
        }),
      });

      if (!response.ok) throw new Error("Failed to update user");

      // Reload the users list after update
      loadUsers();
      editModal.hide(); // Hide the modal after successful edit
    } catch (error) {
      console.error("Error updating user:", error);
    }
  }
});

// Handle user deletion
async function handleDelete(event) {
  const userId = event.target.getAttribute("data-id");

  if (confirm("Are you sure you want to delete this user?")) {
    try {
      const response = await fetch(apiUrl, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: userId }),
      });

      if (!response.ok) throw new Error("Failed to delete user");

      // Reload the users list after deletion
      loadUsers();
    } catch (error) {
      console.error("Error deleting user:", error);
    }
  }
}

// Load the users when the page loads
loadUsers();
