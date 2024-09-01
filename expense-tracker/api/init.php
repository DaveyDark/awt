<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "expense_tracker";

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
          type ENUM('admin', 'user') NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Sheets (
          id INT PRIMARY KEY AUTO_INCREMENT,
          name VARCHAR(255) NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Expenses (
          id INT PRIMARY KEY AUTO_INCREMENT,
          sheet_id INT,
          purpose VARCHAR(255) NOT NULL,
          amount DECIMAL(10, 2) NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (sheet_id) REFERENCES Sheets(id)
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Log (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT,
          action TEXT NOT NULL,
          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES Users(id)
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
