<?php
if (!isset($_GET['id'])) {
  header("Location: ../home");
  exit();
}

$id = $_GET['id'];
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Seating Arrangement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <style>
    table {
      width: fit;
    }

    .seat {
      width: 2em;
      height: 2em;
      border: 2px solid lightgray;
      border-radius: 5px;
      margin: 1px;
    }

    .seat-table {
      padding: 0.5rem;
      display: inline-block;
      border-collapse: collapse;
      border: 2px solid lightgray;
      border-radius: 10px;
    }

    .free {
      background-color: green !important;
    }

    .occupied {
      background-color: red !important;
    }
  </style>
</head>

<body class="container bg-white shadow mt-5">
  <div class="p-5 row">
    <h1 id="arrangementName" class="mb-3">Sitting Arrangement:</h1>
    <h3>Seating Arrangement</h3>
    <div class="bg-light py-5 shadow-sm row">
      <table id="seatingTable" class="seat-table shadow-sm col-auto mx-auto"></table>
    </div>
    <p id="feedback" class="fst-italic fw-light mt-3 text-center"></p>
    <div class="row gap-4 justify-content-end">
      <input type="text" name="roll_number" id="roll_number"
        class="form-control bg-light" placeholder="Roll Number">
      <button id="seatBtn" class="btn btn-primary col-sm-6 col-md-4 col-lg-2 col">Book Seat</button>
    </div>
  </div>
</body>

<script>
  document.page_id = <?php echo $id; ?>;
</script>
<script src="../js/seating.js"></script>

</html>
