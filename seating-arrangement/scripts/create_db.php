<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "seating_arrangement";

try {
  $pdo = new PDO("mysql:host=$servername", $dbusername, $dbpassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");

  $pdo->exec("USE $dbname");

  $createQueries = [
    "
      CREATE TABLE IF NOT EXISTS Users (
          id INT PRIMARY KEY AUTO_INCREMENT,
          name VARCHAR(255) NOT NULL,
          password VARCHAR(255) NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Arrangements (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT NOT NULL,
          row_count INT NOT NULL,
          column_count INT NOT NULL,
          name VARCHAR(255) NOT NULL,
          FOREIGN KEY (user_id) REFERENCES Users(id)
      );

    CREATE TABLE IF NOT EXISTS Seats (
          id INT PRIMARY KEY AUTO_INCREMENT,
          arrangement_id INT NOT NULL,
          row_num INT NOT NULL,
          col_num INT NOT NULL,
          roll_number INT,
          FOREIGN KEY (arrangement_id) REFERENCES Arrangements(id)
    );
    "
  ];

  foreach ($createQueries as $query) {
    $pdo->exec($query);
  }
} catch (PDOException $e) {
  echo "pdo action failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
