<?php
session_start();
require_once "init.php";

try {
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      // Check if user is logged in
      if (!isset($_SESSION["user_id"])) {
        http_response_code(401);
        exit();
      }

      // Return a list of all users of type 'user' who are not deleted
      $query = $pdo->prepare("SELECT id, name, createdAt FROM Users WHERE type = 'user' AND deleted = 0");
      $query->execute();
      echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
      http_response_code(200);
      break;

    case 'POST':
      // Creating a new 'user' user
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['name']) || !isset($data['password'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $name = $data['name'];
      $password = $data['password'];
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert new user into the database
        $query = $pdo->prepare("INSERT INTO Users (name, password, type, deleted) VALUES (:name, :password, 'user', 0)");
        $query->execute([
          'name' => $name,
          'password' => $hashedPassword
        ]);

        // Get the last inserted user ID
        $newUserId = $pdo->lastInsertId();

        // Commit transaction
        $pdo->commit();

        // Return new user data to the frontend
        echo json_encode(['id' => $newUserId, 'name' => $name]);
        http_response_code(201);
      } catch (PDOException $e) {
        // Roll back the transaction in case of error
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
      }
      break;

    case 'PUT':
      // Check if user is logged in
      if (!isset($_SESSION["user_id"])) {
        http_response_code(401);
        exit();
      }

      // Editing a 'user' user's name and password
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['id']) || !isset($data['name']) || !isset($data['password'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $user_id = $data['id'];
      $new_name = $data['name'];
      $new_password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the new password

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the user exists, is of type 'user', and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Users WHERE id = :id AND type = 'user' AND deleted = 0");
        $checkQuery->execute(['id' => $user_id]);
        $user = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
          echo json_encode(['error' => 'User not found']);
          http_response_code(404);
          exit();
        }

        // Update the user's name and password
        $updateQuery = $pdo->prepare("UPDATE Users SET name = :name, password = :password WHERE id = :id");
        $updateQuery->execute([
          'name' => $new_name,
          'password' => $new_password,
          'id' => $user_id
        ]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'User updated successfully']);
        http_response_code(200);
      } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
      }
      break;

    case 'DELETE':
      // Check if user is logged in
      if (!isset($_SESSION["user_id"])) {
        http_response_code(401);
        exit();
      }

      // Soft-delete a 'user' user
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['id'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $user_id = $data['id'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the user exists, is of type 'user', and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Users WHERE id = :id AND type = 'user' AND deleted = 0");
        $checkQuery->execute(['id' => $user_id]);
        $user = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
          echo json_encode(['error' => 'User not found']);
          http_response_code(404);
          exit();
        }

        // Soft-delete the user
        $deleteQuery = $pdo->prepare("UPDATE Users SET deleted = 1 WHERE id = :id");
        $deleteQuery->execute(['id' => $user_id]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'User deleted successfully']);
        http_response_code(200);
      } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
      }
      break;

    default:
      http_response_code(405);
      break;
  }
} catch (PDOException $e) {
  $pdo->rollBack();
  echo json_encode(['error' => $e->getMessage()]);
  http_response_code(500);
}
