<?php
session_start();
if (!isset($_SESSION["sr_user_id"])) {
  header("Location: login");
  exit();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Struck-Off</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="container pt-3">
  <h1 class="text-center mb-2">Struck-Off Students List</h1>
  <div class="d-flex justify-content-end my-2 gap-2">
    <?php
    if ($_SESSION["sr_user_type"] === "l1") {
      echo '<button class="btn btn-primary" id="add-student" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>';
    }
    ?>
    <button class="btn btn-danger" id="logout">Logout</button>
  </div>
  <div class="row gap-4">
    <div class="border bg-light rounded-4 p-4 col shadow-sm">
      <h2>
        Pending Review
      </h2>
      <table class="table table-striped table-bordered table-hover shadow-sm">
        <thead>
          <tr>
            <th>Name</th>
            <th>Roll Number</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="pendingReview">
        </tbody>
      </table>
    </div>
    <div class="border bg-light rounded-4 p-4 col shadow-sm">
      <h2>
        Completed Review
      </h2>
      <table class="table table-striped table-bordered table-hover shadow-sm">
        <thead>
          <tr>
            <th>Name</th>
            <th>Roll Number</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="completedReview">
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addStudentForm" method="post">
            <div class="form-group">
              <label for="student-name">Name</label>
              <input type="text" class="form-control" id="student-name" required name="name">
            </div>
            <div class="form-group">
              <label for="student-roll">Roll number</label>
              <input type="text" class="form-control" id="student-roll" required name="urn">
            </div>
            <button id="submit-student-button" type="submit" class="btn btn-primary mt-3">Add Student</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel">Review Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="reviewForm">
            <div class="form-group">
              <label for="review-name">Name</label>
              <input type="text" class="form-control" id="review-name" disabled>
            </div>
            <div class="form-group">
              <label for="review-roll">Roll number</label>
              <input type="text" class="form-control" id="review-roll" disabled>
            </div>
            <div class="form-group">
              <label for="review-content">Previous Remarks</label>
              <textarea class="form-control" id="review-remarks" rows=6 disabled></textarea>
            </div>
            <div class="form-group" id="review-content-group">
              <label for="review-content">Review</label>
              <textarea class="form-control" id="review-content" rows=6 name="remark" required></textarea>
            </div>
            <div class="form-group" id="status-group">
              <label>Decision</label>
              <br>
              <input type="radio" id="accept" name="status" value="accepted">
              <label for="accept">Accept</label>
              <input type="radio" id="reject" name="status" value="rejected">
              <label for="reject">Reject</label>
            </div>
            <button id="submit-button" type="submit" class="btn btn-primary mt-3">Save Remarks</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    <?php
    echo "const user_type = '{$_SESSION['sr_user_type']}';";
    ?>
  </script>
  <script src="../js/home.js"></script>
</body>

</html>
