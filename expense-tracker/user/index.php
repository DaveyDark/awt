<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: ../login");
  exit();
}
if ($_SESSION["user_type"] !== "admin") {
  header("Location: home");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container pb-5 px-5 pt-3">
    <h1 class="text-center">User Management</h1>
    <h2>Register a New User</h2>
    <form id="registerForm" class="border p-3 rounded shadow-sm">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" name="name" id="name" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" name="password" id="password" required>
      </div>
      <button type="submit" class="my-2 btn btn-primary">Register</button>
    </form>

    <h2 class="mt-3">Users List</h2>
    <table class="table table-striped table-bordered table-responsive my-3">
      <thead>
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Actions</th>
        </tr>

      </thead>
      <tbody id="usersTable">
      </tbody>
    </table>
  </div>

  <!-- Edit User Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <div class="form-group">
              <label for="edit-name">Name</label>
              <input type="text" class="form-control" id="edit-name" required>
            </div>
            <div class="form-group">
              <label for="edit-password">New Password</label>
              <input type="password" class="form-control" id="edit-password" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/user.js"></script>
</body>

</html>
