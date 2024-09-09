<?php
// Check for login
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: ../login");
  exit();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Expense Tracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <!-- Left side - Expense Tracker text -->
    <a class="navbar-brand fs-3" href="#">Expense Tracker</a>

    <!-- Right side - Collapsible content (for mobile responsiveness) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <!-- Right side button -->
      <button class="btn btn-primary mx-2" type="button">Some Button</button>
      
        <button class="btn btn-primary mx-2" type="button" id="addUser">Add User</button>
     
      <form action="../api/logout.php" method="POST">
        <button type="submit" class="btn bg-danger text-white">Logout</button>
      </form>
    </div>
  </div>
</nav>
<div class="container p-4">
    <div class="row gap-4 mb-4">
      <div class="border bg-light rounded-4 p-4 col">
        <h2 class="fs-5 fw-bold">Months:</h2>
        <ul class="pagination px-3 py-1" id="pagination"></ul>
        <form class="row" id="add-item-form">
          <span class="col-5">
            <input
              class="form-control"
              type="text"
              id="item-name"
              name="purpose"
              placeholder="Item Name" />
          </span>
          <span class="col-5">
            <input
              class="form-control"
              type="number"
              id="item-amount"
              name="amount"
              placeholder="Item Amount" />
          </span>
          <span class="col-2">
            <button class="btn btn-primary" id="add-item">Add</button>
          </span>
        </form>
        <table
          class="table table-striped table-bordered table-responsive my-3">
          <thead>
            <tr>
              <th>Item</th>
              <th>Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="items-table">
          </tbody>
        </table>
      </div>
      <div class="border bg-light rounded-4 p-4 col">Graph</div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-item-form">
              <div class="form-group my-2">
                <label for="edit-item-name">Purpose</label>
                <input type="text" class="form-control" id="edit-item-name" name="purpose" required>
              </div>
              <div class="form-group my-2">
                <label for="edit-item-amount">Amount</label>
                <input type="number" class="form-control" id="edit-item-amount" name="amount" required>
              </div>
              <input type="hidden" id="edit-item-id" name="expense_id">
              <button type="submit" class="btn btn-primary my-2">Save changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  
    </div>
  <script src="../js/dashboard.js"></script>
</body>

</html>
