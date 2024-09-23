<?php
session_start();
if (!isset($_SESSION['sta_user_id'])) {
  header("Location: ../login");
  exit();
}

require_once 'db.php'; // Include your PDO connection setup

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (!isset($_GET['date'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing date parameter']);
    exit();
  }

  $date = $_GET['date'];
  try {
    // Fetch all students
    $studentQuery = $pdo->prepare("SELECT urn, name, branch FROM Students WHERE deleted = 0");
    $studentQuery->execute();
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch attendance for the selected date
    $attendanceQuery = $pdo->prepare("SELECT urn FROM Attendance WHERE date = :date");
    $attendanceQuery->execute(['date' => $date]);
    $attendanceRecords = $attendanceQuery->fetchAll(PDO::FETCH_COLUMN);

    // Map attendance to students (present/absent)
    foreach ($students as &$student) {
      $student['present'] = in_array($student['urn'], $attendanceRecords) ? 'yes' : 'no';
    }

    echo json_encode($students);
    http_response_code(200); // OK
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500); // Internal Server Error
  }
}
