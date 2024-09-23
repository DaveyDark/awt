<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['name']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  // Get data from POST request
  $name = $_POST['name'];
  $password = $_POST['password'];

  // Get PDO connection
  require_once "db.php";

  try {
    // Start a transaction
    $pdo->beginTransaction();

    // Get the user from the database
    $query = $pdo->prepare("SELECT * FROM Admins WHERE name = :name");
    $result = $query->execute(['name' => $name]);

    // Compare the password
    if ($result) {
      // Fetch the user
      $user = $query->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        echo json_encode(['error' => 'User not found']);
        http_response_code(401);
        exit();
      }

      // Check if password is correct
      if (password_verify($password, $user['password'])) {
        // Start the session
        session_start();
        $_SESSION['sta_user_id'] = $user['id'];
        $_SESSION['sta_user_name'] = $user['name'];

        // Redirect to dashboard
        http_response_code(200);
        header("Location: ../home");
        exit();
      } else {
        // Return error message
        echo json_encode(['error' => 'Invalid password']);
        http_response_code(401);
      }
    } else {
      echo json_encode(['error' => 'User not found']);
    }
  } catch (PDOException $e) {
    // Rollack any changes if something went wrong
    $pdo->rollBack();
    // Return error message
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
}
