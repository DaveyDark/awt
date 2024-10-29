<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "student_attendance";

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
            branch VARCHAR(3) NOT NULL DEFAULT 'IT',
            deleted BOOLEAN NOT NULL DEFAULT FALSE,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
  $pdo->exec($createTableQuery);

  // Open the CSV file
  $csvFile = '/opt/lampp/htdocs/student-attendance/students.csv'; // Path to your CSV file
  if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip the first row (column headers)
    fgetcsv($handle, 1000, ",");

    // Prepare the SQL insert statement
    $insertQuery = $pdo->prepare("INSERT INTO Students (urn, name, branch) VALUES (:urn, :name, :branch)");

    // Loop through each row in the CSV
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      // Get data from CSV columns
      $urn = $data[0];
      $name = $data[1];

      // Check if branch is provided in the CSV (assumed 3rd column). Default to 'IT' if not.
      $branch = isset($data[2]) ? $data[2] : 'IT';

      // Bind parameters and execute the query
      $insertQuery->bindParam(':urn', $urn);
      $insertQuery->bindParam(':name', $name);
      $insertQuery->bindParam(':branch', $branch);
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
