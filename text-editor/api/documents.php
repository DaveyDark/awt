<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['te_user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit();
}

$user_id = $_SESSION['te_user_id'];

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($_GET['id'])) {
      $doc_id = $_GET['id'];
      try {
        $stmt = $pdo->prepare("SELECT id, user_id, name, content, created_at, updated_at FROM documents WHERE id = :id AND user_id = :user_id AND deleted = 0");
        $stmt->execute(['id' => $doc_id, 'user_id' => $user_id]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($document) {
          echo json_encode($document);
          http_response_code(200); // OK
        } else {
          echo json_encode(['error' => 'Document not found']);
          http_response_code(404); // Not Found
        }
      } catch (PDOException $e) {
        echo json_encode(['error' => 'Error retrieving document']);
        http_response_code(500); // Internal Server Error
      }
    } else {
      try {
        $stmt = $pdo->prepare("SELECT id, name, created_at, updated_at FROM documents WHERE user_id = :user_id AND deleted = 0");
        $stmt->execute(['user_id' => $user_id]);
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($documents);
        http_response_code(200); // OK
      } catch (PDOException $e) {
        echo json_encode(['error' => 'Error retrieving documents' . $e->getMessage()]);
        http_response_code(500); // Internal Server Error
      }
    }
    break;

  case 'POST':
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || !isset($data['content'])) {
      http_response_code(400); // Bad Request
      echo json_encode(['error' => 'Missing required fields']);
      exit();
    }

    $name = $data['name'];
    $content = $data['content'];

    try {
      $stmt = $pdo->prepare("INSERT INTO documents (user_id, name, content) VALUES (:user_id, :name, :content)");
      $stmt->execute(['user_id' => $user_id, 'name' => $name, 'content' => $content]);

      $document_id = $pdo->lastInsertId();
      echo json_encode(['id' => $document_id, 'name' => $name]);
      http_response_code(201); // Created
    } catch (PDOException $e) {
      echo json_encode(['error' => 'Error creating document']);
      http_response_code(500); // Internal Server Error
    }
    break;

  case 'PUT':
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || !isset($data['name']) || !isset($data['content'])) {
      http_response_code(400); // Bad Request
      echo json_encode(['error' => 'Missing required fields']);
      exit();
    }

    $doc_id = $data['id'];
    $name = $data['name'];
    $content = $data['content'];

    try {
      // Check if the document belongs to the user and is not deleted
      $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = :id AND user_id = :user_id AND deleted = 0");
      $stmt->execute(['id' => $doc_id, 'user_id' => $user_id]);
      $document = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$document) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Document not found']);
        exit();
      }

      // Update the document
      $updateStmt = $pdo->prepare("UPDATE documents SET name = :name, content = :content WHERE id = :id AND user_id = :user_id");
      $updateStmt->execute(['name' => $name, 'content' => $content, 'id' => $doc_id, 'user_id' => $user_id]);

      echo json_encode(['message' => 'Document updated successfully']);
      http_response_code(200); // OK
    } catch (PDOException $e) {
      echo json_encode(['error' => 'Error updating document']);
      http_response_code(500); // Internal Server Error
    }
    break;

  case 'DELETE':
    // Soft-delete a document
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
      http_response_code(400); // Bad Request
      echo json_encode(['error' => 'Missing document ID']);
      exit();
    }

    $doc_id = $data['id'];

    try {
      // Check if the document belongs to the user and is not already deleted
      $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = :id AND user_id = :user_id AND deleted = 0");
      $stmt->execute(['id' => $doc_id, 'user_id' => $user_id]);
      $document = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$document) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Document not found']);
        exit();
      }

      // Soft-delete the document
      $deleteStmt = $pdo->prepare("UPDATE documents SET deleted = 1 WHERE id = :id AND user_id = :user_id");
      $deleteStmt->execute(['id' => $doc_id, 'user_id' => $user_id]);

      echo json_encode(['message' => 'Document deleted successfully']);
      http_response_code(200); // OK
    } catch (PDOException $e) {
      echo json_encode(['error' => 'Error deleting document']);
      http_response_code(500); // Internal Server Error
    }
    break;

  default:
    http_response_code(405); // Method Not Allowed
    break;
}
