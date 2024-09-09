<?php
// Database connection details
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "seating_arrangement";

try {
  // Create a new PDO instance with the database connection
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
  // Set PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Clear the tables before seeding
  $pdo->exec("DELETE FROM Seats");
  $pdo->exec("DELETE FROM Arrangements");
  $pdo->exec("DELETE FROM Users");

  // Optional: Reset auto-increment values (if needed)
  $pdo->exec("ALTER TABLE Users AUTO_INCREMENT = 1");
  $pdo->exec("ALTER TABLE Arrangements AUTO_INCREMENT = 1");
  $pdo->exec("ALTER TABLE Seats AUTO_INCREMENT = 1");

  // Define the users to seed
  $users = [
    ['name' => 'admin', 'password' => 'admin']
  ];

  // Seed Users
  $userStmt = $pdo->prepare("INSERT INTO Users (name, password) VALUES (:name, :password)");

  foreach ($users as $user) {
    // Hash the password before inserting
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    // Bind the parameters and execute the statement
    $userStmt->bindParam(':name', $user['name']);
    $userStmt->bindParam(':password', $hashedPassword);
    $userStmt->execute();
  }

  // Seed a sample arrangement 'S213' with 5 columns and 3 rows
  $arrangementStmt = $pdo->prepare("INSERT INTO Arrangements (name, row_count, column_count, user_id) VALUES (:name, :row_count, :column_count, :user_id)");
  $arrangementStmt->execute([
    ':name' => 'S213',
    ':row_count' => 3,
    ':column_count' => 5,
    ':user_id' => 1,
  ]);

  // Get the last inserted arrangement ID
  $arrangementId = $pdo->lastInsertId();

  // Define seats with specific roll numbers
  $seatsWithRollNumbers = [
    ['row' => 1, 'col' => 1, 'roll_number' => 2203818],
    ['row' => 1, 'col' => 3, 'roll_number' => 2203810],
    ['row' => 1, 'col' => 2, 'roll_number' => 2203855],
    ['row' => 1, 'col' => 4, 'roll_number' => 2203839]
  ];

  // Insert all seats, marking some with roll numbers and others as NULL
  $seatStmt = $pdo->prepare("INSERT INTO Seats (arrangement_id, row_num, col_num, roll_number) VALUES (:arrangement_id, :row, :col, :roll_number)");

  // Iterate over the rows and columns
  for ($row = 1; $row <= 3; $row++) {
    for ($col = 1; $col <= 5; $col++) {
      // Check if a seat has a roll number
      $rollNumber = null;
      foreach ($seatsWithRollNumbers as $seat) {
        if ($seat['row'] == $row && $seat['col'] == $col) {
          $rollNumber = $seat['roll_number'];
          break;
        }
      }

      // Insert the seat with either a roll number or NULL
      $seatStmt->execute([
        ':arrangement_id' => $arrangementId,
        ':row' => $row,
        ':col' => $col,
        ':roll_number' => $rollNumber
      ]);
    }
  }

  echo "Database successfully cleared and seeded with users, arrangement 'S213', and seats! \n";
} catch (PDOException $e) {
  echo "Seeding failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
