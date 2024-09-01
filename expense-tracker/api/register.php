<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['name']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit();
  }

  // Get data from POST request
  $name = $_POST['name'];
  $password = $_POST['password'];
  $type = $_POST['type'];

  // Get PDO connection
  require_once "init.php";

  try {
    // Begin a transaction
    $pdo->beginTransaction();

    // Add the user to the database
    $query = $pdo->prepare("INSERT INTO Users (name, password, type) VALUES (:name, :password, :type)");
    $query->execute([
      'name' => $name,
      'password' => password_hash($password, PASSWORD_DEFAULT),
      'type' => $type
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