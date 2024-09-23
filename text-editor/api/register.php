<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirmPassword'])) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];

  if ($password !== $confirmPassword) {
    echo json_encode(['error' => 'Passwords do not match']);
    http_response_code(400);
    exit();
  }

  require_once "db.php";

  try {
    $pdo->beginTransaction();

    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      echo json_encode(['error' => 'Email already exists']);
      http_response_code(400);
      exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insertQuery = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $insertQuery->execute(['email' => $email, 'password' => $hashedPassword]);

    $pdo->commit();

    session_start();
    $_SESSION['te_user_id'] = $pdo->lastInsertId();
    $_SESSION['te_user_email'] = $email;
    $_SESSION['te_user_type'] = 'user';

    http_response_code(200);
    header("Location: ../home");
    exit();
  } catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
    exit();
  }
}
