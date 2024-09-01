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
      // Return a list of all sheets
      $query = $pdo->prepare("SELECT id, name, timestamp FROM Sheets WHERE deleted = 0");
      $query->execute();

      echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
      http_response_code(200);
      break;
    case 'POST':
      // TODO: Create a new sheet
      break;
    case 'PUT':
      // TODO: Rename a sheet
      break;
    case 'DELETE':
      // TODO: Delete a sheet
      break;
    default:
      http_response_code(405);
      break;
  }
} catch (PDOException $e) {
  $pdo->rollBack();
  echo json_encode(['error' => $e->getMessage()]);
  http_response_code(500);
  exit();
}
