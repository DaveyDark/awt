<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "student_attendance";

try {
  // Create a new PDO instance with the database connection
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
  // Set PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // If connection fails, output the error message
  echo "Connection failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
