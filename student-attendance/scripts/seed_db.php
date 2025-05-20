<?php
/**
 * Database Seeding Script
 * 
 * This script seeds the database with initial data for the Student Attendance System.
 * It uses the configuration settings from config/database.php.
 */

// Load database configuration
$config = require_once __DIR__ . '/../config/database.php';
$servername = $config['host'];
$dbusername = $config['username'];
$dbpassword = $config['password'];
$dbname = $config['database'];

try {
    // Create a new PDO instance with the database connection
    $pdo = new PDO(
        "mysql:host=$servername;dbname=$dbname",
        $dbusername,
        $dbpassword
    );
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Clear the tables before seeding
    $pdo->exec("DELETE FROM Attendance");
    $pdo->exec("DELETE FROM Students");
    $pdo->exec("DELETE FROM Admins");

    // Optional: Reset auto-increment values (if needed)
    $pdo->exec("ALTER TABLE Admins AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE Students AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE Attendance AUTO_INCREMENT = 1");

    // Define the admins to seed
    $admins = [["name" => "admin", "password" => "admin"]];

    // Seed Admins
    $adminStmt = $pdo->prepare(
        "INSERT INTO Admins (name, password) VALUES (:name, :password)"
    );

    foreach ($admins as $admin) {
        // Hash the password before inserting
        $hashedPassword = password_hash($admin["password"], PASSWORD_DEFAULT);

        // Bind the parameters and execute the statement
        $adminStmt->bindParam(":name", $admin["name"]);
        $adminStmt->bindParam(":password", $hashedPassword);
        $adminStmt->execute();
    }

    echo "Database successfully cleared and seeded with default admin user\n";
} catch (PDOException $e) {
    echo "Seeding failed: " . $e->getMessage();
    http_response_code(500);
    exit();
}
