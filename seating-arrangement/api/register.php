<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['name']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit();
  }

  // Get data from POST request
  $name = $_POST['name'];
  $password = $_POST['password'];

  // Get PDO connection
  require_once "db.php";

  try {
    // Begin a transaction
    $pdo->beginTransaction();

    // Add the user to the database
    $query = $pdo->prepare("INSERT INTO Users (name, password) VALUES (:name, :password)");
    $query->execute([
      'name' => $name,
      'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    // Commit the transaction
    $pdo->commit();
    http_response_code(201);
  } catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
    exit();
  }
}
