<?php
session_start();
if (!isset($_SESSION['sa_user_id'])) {
  header("Location: ../login");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arrangements Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <div class="container mt-5">
    <h1 class="text-center mb-4">Arrangements Management</h1>

    <!-- Form to Create/Edit Arrangement -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Create New Arrangement</h5>
        <form id="arrangementForm">
          <input type="hidden" id="arrangementId">
          <div class="mb-3">
            <label for="name" class="form-label">Arrangement Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter arrangement name" required>
          </div>
          <div class="mb-3">
            <label for="rowCount" class="form-label">Row Count</label>
            <input type="number" class="form-control" id="rowCount" placeholder="Enter number of rows" required>
          </div>
          <div class="mb-3">
            <label for="columnCount" class="form-label">Column Count</label>
            <input type="number" class="form-control" id="columnCount" placeholder="Enter number of columns" required>
          </div>
          <button type="submit" class="btn btn-primary">Save Arrangement</button>
          <button type="button" id="cancelEdit" class="btn btn-secondary">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Table for Arrangements -->
    <table class="table table-bordered" id="arrangementsTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Rows</th>
          <th>Columns</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="arrangementsList">
        <!-- Arrangements will be populated here -->
      </tbody>
    </table>
  </div>

  <script src="../js/admin.js"></script>

</body>

</html>
