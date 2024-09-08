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
  $pdo->exec("DELETE FROM Users");
  $pdo->exec("DELETE FROM Arrangements");
  $pdo->exec("DELETE FROM Seats");

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
  echo "Database successfully cleared and seeded! \n";
} catch (PDOException $e) {
  echo "Seeding failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
