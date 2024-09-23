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
  require_once "init.php";

  try {
    // Start a transaction
    $pdo->beginTransaction();

    // Get the user from the database
    $query = $pdo->prepare("SELECT * FROM Users WHERE name = :name");
    $result = $query->execute(['name' => $name]);

    // Compare the password
    if ($result) {
      // Fetch the user
      $user = $query->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        http_response_code(401);
        header("Location: ../login");
        exit();
      }

      $log_query = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
      // Check if password is correct
      if (password_verify($password, $user['password'])) {
        // Add login to log
        $log_query->execute([
          'user_id' => $user['id'],
          'action' => 'Login'
        ]);
        $pdo->commit();

        // Start the session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_type'] = $user['type'];

        // Redirect to dashboard
        http_response_code(200);
        header("Location: ../dashboard");
        exit();
      } else {
        // Add failed login to log
        $log_query->execute([
          'user_id' => $user['id'],
          'action' => 'Failed login'
        ]);
        $pdo->commit();

        // Return error message
        http_response_code(401);
        header("Location: ../login");
      }
    } else {
      header("Location: ../login");
    }
  } catch (PDOException $e) {
    // Rollack any changes if something went wrong
    $pdo->rollBack();
    // Return error message
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
}
