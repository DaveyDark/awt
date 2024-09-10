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
      // Return a list of all sheets that are not deleted
      $query = $pdo->prepare("SELECT id, name, timestamp FROM Sheets WHERE deleted = 0");
      $query->execute();
      echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
      http_response_code(200);
      break;

    case 'POST':
      // Creating a new sheet
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['name'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $sheet_name = $data['name'];

      try {
        // Begin transaction
        $pdo->beginTransaction();
        // Insert new sheet into the database
        $query = $pdo->prepare("INSERT INTO Sheets (name, deleted) VALUES (:name, 0)");
        $query->execute(['name' => $sheet_name]);

        // Get the last inserted sheet ID
        $newSheetId = $pdo->lastInsertId();

        // Commit transaction
        $pdo->commit();

        // Return new sheet data to the frontend
        echo json_encode(['id' => $newSheetId, 'name' => $sheet_name]);
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

      // Updating a sheet's name
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['id']) || !isset($data['name'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $sheet_id = $data['id'];
      $new_name = $data['name'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the sheet exists and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Sheets WHERE id = :id AND deleted = 0");
        $checkQuery->execute(['id' => $sheet_id]);
        $sheet = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$sheet) {
          echo json_encode(['error' => 'Sheet not found']);
          http_response_code(404);
          exit();
        }

        // Update the sheet name
        $updateQuery = $pdo->prepare("UPDATE Sheets SET name = :name WHERE id = :id");
        $updateQuery->execute([
          'name' => $new_name,
          'id' => $sheet_id
        ]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'Sheet updated successfully']);
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

      // Soft-delete a sheet
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['id'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400);
        exit();
      }
      $sheet_id = $data['id'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the sheet exists and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Sheets WHERE id = :id AND deleted = 0");
        $checkQuery->execute(['id' => $sheet_id]);
        $sheet = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$sheet) {
          echo json_encode(['error' => 'Sheet not found']);
          http_response_code(404);
          exit();
        }

        // Soft-delete the sheet
        $deleteQuery = $pdo->prepare("UPDATE Sheets SET deleted = 1 WHERE id = :id");
        $deleteQuery->execute(['id' => $sheet_id]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'Sheet deleted successfully']);
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
