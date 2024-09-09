<?php
session_start();
$loggedIn = $_SESSION['sa_user_id'] ?? false;
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Seating Arrangement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="container-lg py-3">
  <h1 class="mb-5">Sitting Arrangements:</h1>
  <div class="d-flex gap-2 mb-3 justify-content-end">
    <?php
    if ($loggedIn) {
      echo '<a href="../admin" class="btn btn-primary">Admin Panel</a>';
      echo '<a href="../api/logout.php" class="btn btn-danger">Logout</a>';
    } else {
      echo '<a href="../login" class="btn btn-primary">Login</a>';
    }
    ?>
  </div>
  <div class="row justify-content-center">
    <div class="col-sm-12 col-md-8 col-lg-6">
      <table class="table table-striped table-hover table-responsive table-bordered cursor-pointer shadow"
        id="arrangements-table" style="cursor: pointer; width: 100%;">
        <thead>
          <tr>
            <th>Name</th>
            <th>Size</th>
          </tr>
        </thead>
        <tbody id="arrangements-table">
        </tbody>
      </table>
    </div>
  </div>
</body>

<script src="../js/home.js"></script>

</html>
