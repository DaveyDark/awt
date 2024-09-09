<?php

require_once "db.php";

session_start();
$json = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = $_GET['id'] ?? null;
  try {
    if (!$id) {
      $query = $pdo->prepare("SELECT * FROM Arrangements");
      $query->execute();
      $arrangements = $query->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($arrangements);
      die();
    } else {
      $query = $pdo->prepare("SELECT * FROM Arrangements WHERE id = :id");
      $query->bindParam(':id', $id);
      $query->execute();
      $arrangement = $query->fetch(PDO::FETCH_ASSOC);
      if (!$arrangement) {
        echo json_encode(['error' => 'Arrangement not found']);
        http_response_code(404);
        exit();
      }
      $seatsQuery = $pdo->prepare("SELECT * FROM Seats WHERE arrangement_id = :id");
      $seatsQuery->bindParam(':id', $id);
      $seatsQuery->execute();
      $seats = $seatsQuery->fetchAll(PDO::FETCH_ASSOC);
      $arrangement['seats'] = $seats;
      echo json_encode($arrangement);
      die();
    }
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
    exit();
  }
}

if (!isset($_SESSION['sa_user_id'])) {
  echo json_encode(['error' => 'Unauthorized']);
  http_response_code(401);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['row_count']) || !isset($_POST['column_count']) || !isset($_POST['name'])) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  $row_count = $_POST['row_count'];
  $column_count = $_POST['column_count'];
  $name = $_POST['name'];

  try {
    $pdo->beginTransaction();
    $query = $pdo->prepare("INSERT INTO Arrangements (user_id, row_count, column_count, name) VALUES (:user_id, :row_count, :column_count, :name)");
    $query->bindParam(':user_id', $_SESSION['sa_user_id']);
    $query->bindParam(':row_count', $row_count);
    $query->bindParam(':column_count', $column_count);
    $query->bindParam(':name', $name);
    $query->execute();
    $seatsQuery = $pdo->prepare("INSERT INTO Seats (arrangement_id, row_num, col_num) VALUES (:arrangement_id, :row, :column)");
    $arrangement_id = $pdo->lastInsertId();
    $seatsQuery->bindParam(':arrangement_id', $arrangement_id);
    for ($i = 1; $i <= $row_count; $i++) {
      for ($j = 1; $j <= $column_count; $j++) {
        $seatsQuery->bindParam(':row', $i);
        $seatsQuery->bindParam(':column', $j);
        $seatsQuery->execute();
      }
    }
    $pdo->commit();
    echo json_encode(['message' => 'Arrangement created']);
  } catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  if (!isset($json->id)) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }
  $id = $json->id;
  $name = $json->name ?? null;
  $row_count = $json->row_count ?? null;
  $column_count = $json->column_count ?? null;

  try {
    $existingQuery = $pdo->prepare("SELECT * FROM Arrangements WHERE id = :id");
    $existingQuery->bindParam(':id', $id);
    $existingQuery->execute();
    $existing = $existingQuery->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
      echo json_encode(['error' => 'Arrangement not found']);
      http_response_code(404);
      exit();
    }
    $name = $name ? $name : $existing['name'];
    $row_count = $row_count ? $row_count : $existing['row_count'];
    $column_count = $column_count ? $column_count : $existing['column_count'];
    $query = $pdo->prepare("UPDATE Arrangements SET name = :name, row_count = :row_count, column_count = :column_count WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->bindParam(':name', $name);
    $query->bindParam(':row_count', $row_count);
    $query->bindParam(':column_count', $column_count);
    $query->execute();

    echo json_encode(['message' => 'Arrangement updated']);
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  if (!isset($json->id)) {
    echo json_encode(['error' => 'Missing required data']);
    http_response_code(400);
    exit();
  }

  $id = $json->id;

  try {
    $existingQuery = $pdo->prepare("SELECT * FROM Arrangements WHERE id = :id");
    $existingQuery->bindParam(':id', $id);
    $existingQuery->execute();
    $existing = $existingQuery->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
      echo json_encode(['error' => 'Arrangement not found']);
      http_response_code(404);
      exit();
    }
    $pdo->beginTransaction();
    $seatsQuery = $pdo->prepare("DELETE FROM Seats WHERE arrangement_id = :id");
    $seatsQuery->bindParam(':id', $id);
    $seatsQuery->execute();

    $query = $pdo->prepare("DELETE FROM Arrangements WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
    $pdo->commit();

    echo json_encode(['message' => 'Arrangement deleted']);
  } catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
  }
} else {
  echo json_encode(['error' => 'Method not allowed']);
  http_response_code(405);
}
