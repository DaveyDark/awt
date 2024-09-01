<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_tracker";

try {
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");

    $pdo->exec("USE $dbname");

    $createQuery = "
        CREATE TABLE IF NOT EXISTS expenses (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            amount FLOAT NOT NULL,
            month INT(2) NOT NULL,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    $pdo->exec($createQuery);
} catch(PDOException $e) {
  echo "pdoection failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}