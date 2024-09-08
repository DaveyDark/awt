<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "student_review";

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
          type ENUM('l1', 'l2', 'l3', 'l4') NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Students (
          id INT PRIMARY KEY AUTO_INCREMENT,
          name VARCHAR(255) NOT NULL,
          urn VARCHAR(7) NOT NULL UNIQUE,
          remark1 VARCHAR(1024),
          remark2 VARCHAR(1024),
          remark3 VARCHAR(1024),
          remark4 VARCHAR(1024),
          status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
