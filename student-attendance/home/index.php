<?php
session_start();
if (!isset($_SESSION['sta_user_id'])) {
  header("Location: ../login");
  exit();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Attendance Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"> </script>
</head>

<div class="container py-2">
  <div class="d-flex flex-sm-row flex-column justify-content-between align-items-center bg-primary text-white p-2 rounded">
    <h1>Student Attendance Manager</h1>
    <div class="d-flex flex-sm-row flex-column gap-2">
      <a href="../attendance" class="btn btn-light">Show Attendance</a>
      <a href="../students" class="btn btn-light">Manage Students</a>
      <a href="../api/logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <div id="alert-placeholder" class="mt-3"></div>

  <div class="row p-2 bg-light rounded justify-content-center mt-4">
    <h2><strong>QR</strong> Scanner</h2>
    <div id="reader" class="col-sm-12 col-md-8 col-lg-6 mx-auto"></div>
  </div>

  <script src="../js/home.js"></script>
</div>

</html>
