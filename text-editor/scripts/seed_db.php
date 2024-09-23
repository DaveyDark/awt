<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "text_editor";

// Insert seed user data
try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $hashedPassword = password_hash('pw', PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
  $stmt->execute(['email' => 'devesh@gmail.com', 'password' => $hashedPassword]);
  echo "User seeded successfully\n";
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
