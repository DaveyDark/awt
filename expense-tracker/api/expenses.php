<?php
session_start();
require_once "init.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  exit();
}

$json = json_decode(file_get_contents("php://input"));

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (!isset($_GET['sheet_id'])) {
      echo json_encode(['error' => 'Missing required data']);
      http_response_code(400);
      exit();
    }

    // Get sheet_id from GET request
    $sheet_id = $_GET['sheet_id'];

    // Return a list of all expenses
    $query = $pdo->prepare("SELECT id, purpose, amount, timestamp FROM Expenses WHERE deleted = 0 AND sheet_id = :sheet_id");
    $query->execute(['sheet_id' => $sheet_id]);
    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    http_response_code(200);
    break;
  case 'POST':
    // Check for missing data
    if (!isset($_POST['sheet_id']) || !isset($_POST['purpose']) || !isset($_POST['amount'])) {
      echo json_encode(['error' => 'Missing required data']);
      http_response_code(400);
      exit();
    }
    // Get data from POST request
    $sheet_id = $_POST['sheet_id'];
    $purpose = $_POST['purpose'];
    $amount = $_POST['amount'];

    try {
      // Begin a transaction
      $pdo->beginTransaction();
      // Add the expense to the database
      $query = $pdo->prepare("INSERT INTO Expenses (sheet_id, purpose, amount) VALUES (:sheet_id, :purpose, :amount)");
      $query->execute([
        'sheet_id' => $sheet_id,
        'purpose' => $purpose,
        'amount' => $amount
      ]);
      // Add log 
      $logQuery = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
      $username = $_SESSION['user_name'];
      $logQuery->execute([
        'user_id' => $_SESSION["user_id"],
        'action' => "Added expense of $amount to sheet $sheet_id"
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
    break;
  case 'PUT':
    // Check for missing data
    if (!isset($json->expense_id)) {
      echo json_encode(['error' => 'Missing required data']);
      http_response_code(400);
      exit();
    }

    // Get data 
    $expense_id = $json->expense_id;
    $purpose = $json->purpose ?? null;
    $amount = $json->amount ?? null;

    try {
      // Check if expense exists
      $expenseQuery = $pdo->prepare("SELECT * FROM Expenses WHERE id = :id AND deleted = 0");
      $expenseQuery->execute(['id' => $expense_id]);
      $expense = $expenseQuery->fetch(PDO::FETCH_ASSOC);
      if (!$expense) {
        echo json_encode(['error' => 'Expense not found']);
        http_response_code(404);
        exit();
      }

      // Begin a transaction
      $pdo->beginTransaction();
      $query = $pdo->prepare("UPDATE Expenses SET purpose = :purpose, amount = :amount WHERE id = :id");
      $query->execute([
        'purpose' => $purpose ?? $expense['purpose'],
        'amount' => $amount ?? $expense['amount'],
        'id' => $expense_id
      ]);

      // Add log
      $logQuery = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
      $logQuery->execute([
        'user_id' => $_SESSION["user_id"],
        'action' => "Updated expense $expense_id"
      ]);
      $pdo->commit();

      http_response_code(200);
    } catch (PDOException $e) {
      $pdo->rollBack();
      echo json_encode(['error' => $e->getMessage()]);
      http_response_code(500);
      exit();
    }

    break;
  case 'DELETE':
    // Check for missing data
    if (!isset($json->expense_id)) {
      echo json_encode(['error' => 'Missing required data']);
      http_response_code(400);
      exit();
    }

    // Get expense_id from DELETE request
    $expense_id = $json->expense_id;

    try {
      // Check if expense exists
      $expenseQuery = $pdo->prepare("SELECT * FROM Expenses WHERE id = :id AND deleted = 0");
      $expenseQuery->execute(['id' => $expense_id]);
      $expense = $expenseQuery->fetch(PDO::FETCH_ASSOC);
      if (!$expense) {
        echo json_encode(['error' => 'Expense not found']);
        http_response_code(404);
        exit();
      }

      // Begin a transaction
      $pdo->beginTransaction();
      $query = $pdo->prepare("UPDATE Expenses SET deleted = 1 WHERE id = :id");
      $query->execute(['id' => $expense_id]);

      $logQuery = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
      $logQuery->execute([
        'user_id' => $_SESSION["user_id"],
        'action' => "Deleted expense $expense_id"
      ]);

      $pdo->commit();
      http_response_code(204);
    } catch (PDOException $e) {
      $pdo->rollBack();
      echo json_encode(['error' => $e->getMessage()]);
      http_response_code(500);
      exit();
    }
    break;
  default:
    http_response_code(405);
}
