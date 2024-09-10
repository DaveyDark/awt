<?php
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is authenticated (user_id, user_name, and user_type should be in session)
if (!isset($_SESSION['sr_user_id']) || !isset($_SESSION['sr_user_name']) || !isset($_SESSION['sr_user_type'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit();
}

// User is authenticated
$userType = $_SESSION['sr_user_type'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  try {
    // Fetch students based on user type (l1, l2, l3, l4) and those with accepted or rejected status
    $query = "SELECT * FROM Students WHERE ";

    // Filter logic based on the user's level
    if ($userType === 'l1') {
      $query .= "remark1 IS NULL AND remark2 IS NULL AND remark3 IS NULL AND remark4 IS NULL";
    } elseif ($userType === 'l2') {
      $query .= "remark1 IS NOT NULL AND remark2 IS NULL AND remark3 IS NULL AND remark4 IS NULL";
    } elseif ($userType === 'l3') {
      $query .= "remark2 IS NOT NULL AND remark3 IS NULL AND remark4 IS NULL";
    } elseif ($userType === 'l4') {
      $query .= "remark3 IS NOT NULL AND remark4 IS NULL";
    }

    // Students with all remarks filled will have status 'accepted' or 'rejected'
    $query .= " OR status IN ('accepted', 'rejected')";

    $stmt = $pdo->query($query);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the students as a JSON response
    echo json_encode($students);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    http_response_code(500);
    exit();
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle the request to add a remark
  $student_id = $_POST['student_id'] ?? null;

  if (!$student_id) {
    // If no student_id is given, add a new student with the provided details
    $name = $_POST['name'] ?? null;
    $urn = $_POST['urn'] ?? null;
    if (!$name || !$urn) {
      http_response_code(400);
      echo json_encode(['error' => 'name and urn are required']);
      exit();
    }

    try {
      $query = $pdo->prepare("INSERT INTO Students (name, urn, status) VALUES (:name, :urn, 'pending')");
      $query->bindParam(':name', $name);
      $query->bindParam(':urn', $urn);
      $query->execute();

      echo json_encode(['success' => 'Student added successfully']);
      die();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      http_response_code(500);
      exit();
    }
  }

  $remark = $_POST['remark'] ?? null;
  $status = $_POST['status'] ?? null; // For l4 users, the status must also be provided

  if (!$student_id || !$remark || ($userType === 'l4' && !$status)) {
    http_response_code(400);
    echo json_encode(['error' => 'student_id, remark, and (for l4) status are required']);
    exit();
  }

  try {
    // Determine which remark field to update based on user type
    $remarkField = null;
    if ($userType === 'l1') {
      $remarkField = 'remark1';
    } elseif ($userType === 'l2') {
      $remarkField = 'remark2';
    } elseif ($userType === 'l3') {
      $remarkField = 'remark3';
    } elseif ($userType === 'l4') {
      $remarkField = 'remark4';
    }

    // If we have a valid field for the user level, update the student's remark
    if ($remarkField) {
      // If the user is l4, also update the status
      if ($userType === 'l4') {
        $stmt = $pdo->prepare("UPDATE Students SET $remarkField = :remark, status = :status WHERE id = :student_id");
        $stmt->bindParam(':status', $status);
      } else {
        $stmt = $pdo->prepare("UPDATE Students SET $remarkField = :remark WHERE id = :student_id");
      }

      // Bind common parameters and execute
      $stmt->bindParam(':remark', $remark);
      $stmt->bindParam(':student_id', $student_id);
      $stmt->execute();

      echo json_encode(['success' => 'Remark updated successfully']);
    } else {
      http_response_code(403);
      echo json_encode(['error' => 'You are not authorized to add remarks']);
    }
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    http_response_code(500);
    exit();
  }
} else {
  // Method not allowed
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit();
}
