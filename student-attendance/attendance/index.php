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
  <title>Student Attendance</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body class="container p-3">
  <h1>Student Attendance</h1>
  <div class="d-flex flex-sm-row flex-column gap-2">
    <a href="../home" class="btn btn-danger">Back</a>
  </div>
  <div class="row my-3">
    <div class="col-lg-6 col-sm-12 mx-auto p-3 bg-light rounded shadow">
      <input type="date" id="date-input" class="form-control mb-3" />
      <table class="table table-striped table-bordered table-responsive">
        <thead>
          <tr>
            <th class="col-2">URN</th>
            <th class="col-4">Name</th>
            <th class="col-2">Branch</th>
            <th class="col-4">Present</th>
          </tr>
        </thead>
        <tbody id="students-table"></tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/attendance.js"></script>
</body>

</html>
