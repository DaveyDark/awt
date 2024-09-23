<?php
session_start();
require_once "db.php"; // Assuming init.php contains the PDO connection setup

try {
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if (!isset($_SESSION["sta_user_id"])) {
        http_response_code(401); // Unauthorized
        exit();
      }

      if (isset($_GET['urn'])) {
        $urn = $_GET['urn'];

        // Prepare a query to get the details of the specific student
        $query = $pdo->prepare("SELECT urn, name, branch, createdAt FROM Students WHERE urn = :urn AND deleted = 0");
        $query->execute(['urn' => $urn]);

        $student = $query->fetch(PDO::FETCH_ASSOC);

        // If the student is not found, return a 404 status
        if (!$student) {
          echo json_encode(['error' => 'Student not found']);
          http_response_code(404); // Not Found
          exit();
        }

        // Return the specific student details
        echo json_encode($student);
        http_response_code(200); // OK

      } else {
        // Return a list of all students that are not deleted
        $query = $pdo->prepare("SELECT urn, name, branch, createdAt FROM Students WHERE deleted = 0");
        $query->execute();
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
        http_response_code(200); // OK
      }
      break;
    case 'POST':
      // Check if user is logged in
      if (!isset($_SESSION["sta_user_id"])) {
        http_response_code(401); // Unauthorized
        exit();
      }

      // Creating a new student
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['urn']) || !isset($data['name']) || !isset($data['branch'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400); // Bad Request
        exit();
      }

      $urn = $data['urn'];
      $name = $data['name'];
      $branch = $data['branch'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert new student into the database
        $query = $pdo->prepare("INSERT INTO Students (urn, name, branch, deleted) VALUES (:urn, :name, :branch, 0)");
        $query->execute([
          'urn' => $urn,
          'name' => $name,
          'branch' => $branch
        ]);

        // Commit transaction
        $pdo->commit();

        // Return new student data to the frontend
        echo json_encode(['urn' => $urn, 'name' => $name, 'branch' => $branch]);
        http_response_code(201); // Created
      } catch (PDOException $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500); // Internal Server Error
      }
      break;

    case 'PUT':
      // Check if user is logged in
      if (!isset($_SESSION["sta_user_id"])) {
        http_response_code(401); // Unauthorized
        exit();
      }

      // Updating a student's details
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['urn']) || !isset($data['name']) || !isset($data['branch'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400); // Bad Request
        exit();
      }

      $urn = $data['urn'];
      $name = $data['name'];
      $branch = $data['branch'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the student exists and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Students WHERE urn = :urn AND deleted = 0");
        $checkQuery->execute(['urn' => $urn]);
        $student = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
          echo json_encode(['error' => 'Student not found']);
          http_response_code(404); // Not Found
          exit();
        }

        // Update student details
        $updateQuery = $pdo->prepare("UPDATE Students SET name = :name, branch = :branch WHERE urn = :urn");
        $updateQuery->execute([
          'name' => $name,
          'branch' => $branch,
          'urn' => $urn
        ]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'Student updated successfully']);
        http_response_code(200); // OK
      } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500); // Internal Server Error
      }
      break;

    case 'DELETE':
      // Check if user is logged in
      if (!isset($_SESSION["sta_user_id"])) {
        http_response_code(401); // Unauthorized
        exit();
      }

      // Soft-delete a student
      $data = json_decode(file_get_contents('php://input'), true);
      if (!isset($data['urn'])) {
        echo json_encode(['error' => 'Missing required data']);
        http_response_code(400); // Bad Request
        exit();
      }

      $urn = $data['urn'];

      try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the student exists and is not deleted
        $checkQuery = $pdo->prepare("SELECT * FROM Students WHERE urn = :urn AND deleted = 0");
        $checkQuery->execute(['urn' => $urn]);
        $student = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
          echo json_encode(['error' => 'Student not found']);
          http_response_code(404); // Not Found
          exit();
        }

        // Soft-delete the student (mark as deleted)
        $deleteQuery = $pdo->prepare("UPDATE Students SET deleted = 1 WHERE urn = :urn");
        $deleteQuery->execute(['urn' => $urn]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['message' => 'Student deleted successfully']);
        http_response_code(200); // OK
      } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500); // Internal Server Error
      }
      break;

    default:
      http_response_code(405); // Method Not Allowed
      break;
  }
} catch (PDOException $e) {
  $pdo->rollBack();
  echo json_encode(['error' => $e->getMessage()]);
  http_response_code(500); // Internal Server Error
}
