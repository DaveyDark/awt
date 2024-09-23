<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  require_once "db.php";

  try {
    $pdo->beginTransaction();

    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $result = $query->execute(['email' => $email]);

    if ($result) {
      $user = $query->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        header("Location: ../login");
        die();
      }

      if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['te_user_id'] = $user['id'];
        $_SESSION['te_user_email'] = $user['email'];
        $_SESSION['te_user_type'] = $user['type'];

        http_response_code(200);
        header("Location: ../home");
        exit();
      } else {
        http_response_code(401);
        header("Location: ../login");
        die();
      }
    } else {
      header("Location: ../login");
      die();
    }
  } catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
    die();
  }
}
