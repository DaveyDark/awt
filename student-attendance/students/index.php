<?php
session_start();
if (!isset($_SESSION['sta_user_id'])) {
  header("Location: ../login");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Manage Students</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>

<body class="container p-3">
  <h1>Manage Students</h1>
  <div class="d-flex justify-content-between my-2">
    <a href="../home" class="btn btn-danger">Back</a>
    <button class="btn btn-primary" id="add-student-btn" data-bs-toggle="modal" data-bs-target="#studentModal">Add Student</button>
  </div>

  <table class="table table-striped table-bordered table-responsive">
    <thead>
      <tr>
        <th class="col-1">URN</th>
        <th class="col-3">Name</th>
        <th class="col-1">Branch</th>
        <th class="col-2">Phone</th>
        <th class="col-3">Email</th>
        <th class="col-2">Actions</th>
      </tr>
    </thead>
    <tbody id="students-table"></tbody>
  </table>

  <!-- Modal for Adding/Editing Student -->
  <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="studentForm">
            <input type="hidden" id="id" name="id">
            <div class="mb-3">
              <label for="urn" class="form-label">URN</label>
              <input type="text" class="form-control" id="urn" name="urn" required>
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="branch" class="form-label">Branch</label>
              <input type="text" class="form-control" id="branch" name="branch" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            <button type="submit" class="btn btn-primary" id="saveStudentBtn">Save</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/students.js"></script>
</body>

</html>
