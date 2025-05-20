<?php
/**
 * CSV Import Script
 * 
 * This script imports student data from a CSV file into the database.
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

  // Create the Students table if it doesn't exist
  $createTableQuery = "
        CREATE TABLE IF NOT EXISTS Students (
            urn VARCHAR(7) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            branch VARCHAR(10) NOT NULL DEFAULT 'IT',
            phone VARCHAR(15) NULL,
            email VARCHAR(255) NULL,
            deleted BOOLEAN NOT NULL DEFAULT FALSE,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
  $pdo->exec($createTableQuery);

  // Open the CSV file
  $csvFile = __DIR__ . '/../students.csv'; // Path to your CSV file
  if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip the first row (column headers)
    fgetcsv($handle, 1000, ",");

    // Prepare the SQL insert statement
    $insertQuery = $pdo->prepare("INSERT INTO Students (urn, name, branch, phone, email) VALUES (:urn, :name, :branch, :phone, :email)");

    // Loop through each row in the CSV
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      // Get data from CSV columns
      $urn = $data[0];
      $name = $data[1];

      // Always set branch to 'IT' since it's not in our CSV
      $branch = 'IT';
      
      // Get phone and email directly from CSV columns
      // Our CSV follows the format: urn,name,phone,email
      $phone = isset($data[2]) ? $data[2] : null;
      $email = isset($data[3]) ? $data[3] : null;

      // Bind parameters and execute the query
      $insertQuery->bindParam(':urn', $urn);
      $insertQuery->bindParam(':name', $name);
      $insertQuery->bindParam(':branch', $branch);
      $insertQuery->bindParam(':phone', $phone);
      $insertQuery->bindParam(':email', $email);
      $insertQuery->execute();
    }

    fclose($handle);
    echo "Data imported successfully!";
  } else {
    echo "Unable to open the CSV file.";
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$pdo = null;
