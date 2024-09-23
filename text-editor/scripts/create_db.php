<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "text_editor";

try {
  $pdo = new PDO("mysql:host=$servername", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Create the database
  $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
  echo "Database created successfully\n";

  // Select the database
  $pdo->exec("USE $dbname");

  // Create users table
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            name VARCHAR(255) NOT NULL,
            content TEXT,
            deleted BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");
  echo "Table 'users' created successfully\n";
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
