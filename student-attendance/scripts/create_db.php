<?php
/**
 * Database Creation Script
 * 
 * This script creates the database and tables for the Student Attendance System.
 * It uses the configuration settings from config/database.php.
 */

// Load database configuration
$config = require_once __DIR__ . '/../config/database.php';
$servername = $config['host'];
$dbusername = $config['username'];
$dbpassword = $config['password'];
$dbname = $config['database'];

try {
  $pdo = new PDO("mysql:host=$servername", $dbusername, $dbpassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");

  $pdo->exec("USE $dbname");

  $createQueries = [
    "
      CREATE TABLE IF NOT EXISTS Admins (
          id INT PRIMARY KEY AUTO_INCREMENT,
          name VARCHAR(255) NOT NULL,
          password VARCHAR(255) NOT NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Students (
          urn VARCHAR(7) PRIMARY KEY,
          name VARCHAR(255) NOT NULL,
          branch VARCHAR(10) NOT NULL,
          phone VARCHAR(15) NULL,
          email VARCHAR(255) NULL,
          deleted BOOLEAN NOT NULL DEFAULT FALSE,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    ",
    "
      CREATE TABLE IF NOT EXISTS Attendance (
          id INT PRIMARY KEY AUTO_INCREMENT,
          urn VARCHAR(7) NOT NULL,
          date DATE NOT NULL,
          createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (urn) REFERENCES Students(urn)
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
