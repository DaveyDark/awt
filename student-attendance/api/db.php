<?php
/**
 * Database Connection
 * 
 * This file establishes a connection to the database using the configuration settings
 * from config/database.php.
 */

// Load database configuration
$config = require_once __DIR__ . '/../config/database.php';
$servername = $config['host'];
$dbusername = $config['username'];
$dbpassword = $config['password'];
$dbname = $config['database'];
$port = $config['port'];
$charset = $config['charset'];

try {
  // Create a new PDO instance with the database connection
  $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=$charset";
  $pdo = new PDO($dsn, $dbusername, $dbpassword);
  // Set PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // If connection fails, output the error message
  echo "Connection failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
