<?php

require_once "db.php";

session_start();
$json = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($json->roll_number) || !isset($json->arrangement_id)) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  $arrangement_id = $json->arrangement_id;
  $roll_number = $json->roll_number;

  try {
    // Fetch the arrangement by ID
    $arrangementQuery = $pdo->prepare("SELECT * FROM Arrangements WHERE id = :id");
    $arrangementQuery->bindParam(':id', $arrangement_id);
    $arrangementQuery->execute();
    $arrangement = $arrangementQuery->fetch(PDO::FETCH_ASSOC);

    if (!$arrangement) {
      echo json_encode(['error' => 'Arrangement not found']);
      http_response_code(404);
      exit();
    }

    // Check if the roll number is already seated in the arrangement
    $checkRollNumberQuery = $pdo->prepare("SELECT * FROM Seats WHERE arrangement_id = :id AND roll_number = :roll_number");
    $checkRollNumberQuery->bindParam(':id', $arrangement_id);
    $checkRollNumberQuery->bindParam(':roll_number', $roll_number);
    $checkRollNumberQuery->execute();
    $existingSeat = $checkRollNumberQuery->fetch(PDO::FETCH_ASSOC);

    if ($existingSeat) {
      // Roll number is already seated
      echo json_encode([
        'error' => 'Roll number already seated',
        'seat' => $existingSeat
      ]);
      http_response_code(409); // Conflict
      exit();
    }

    // Fetch available seats for the arrangement
    $seatsQuery = $pdo->prepare("SELECT * FROM Seats WHERE arrangement_id = :id AND roll_number IS NULL ORDER BY row_num, col_num");
    $seatsQuery->bindParam(':id', $arrangement_id);
    $seatsQuery->execute();
    $seats = $seatsQuery->fetchAll(PDO::FETCH_ASSOC);

    if (empty($seats)) {
      echo json_encode(['error' => 'No available seats']);
      http_response_code(404);
      exit();
    }

    // Now we need to allocate the roll number based on the odd/even seating rule
    $seat_to_assign = null;

    foreach ($seats as $seat) {
      $is_adjacent_seat_even = (($seat['row_num'] + $seat['col_num']) % 2 == 0); // Alternation pattern
      $is_roll_number_even = ($roll_number % 2 == 0);

      // Check the seat pattern - even seats for even roll numbers, odd seats for odd roll numbers
      if (($is_adjacent_seat_even && $is_roll_number_even) || (!$is_adjacent_seat_even && !$is_roll_number_even)) {
        $seat_to_assign = $seat;
        break;
      }
    }

    if ($seat_to_assign) {
      // Assign the seat with the roll number
      $assignSeatQuery = $pdo->prepare("UPDATE Seats SET roll_number = :roll_number WHERE id = :id");
      $assignSeatQuery->bindParam(':roll_number', $roll_number);
      $assignSeatQuery->bindParam(':id', $seat_to_assign['id']);
      $assignSeatQuery->execute();

      echo json_encode([
        'message' => 'Seat assigned successfully',
        'seat' => $seat_to_assign
      ]);
    } else {
      echo json_encode(['error' => 'No suitable seat found']);
      http_response_code(404);
    }
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
} else {
  echo json_encode(['error' => 'Invalid request method']);
  http_response_code(405);
}
