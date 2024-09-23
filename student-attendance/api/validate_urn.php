<?php
session_start();
if (!isset($_SESSION['sta_user_id'])) {
  header('Content-Type: application/json');
  echo json_encode(['error' => 'Unauthorized']);
  http_response_code(401);
  exit();
}

require_once 'db.php'; // Include the database connection

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['urn'])) {
  echo json_encode(['error' => 'Missing URN']);
  http_response_code(400); // Bad Request
  exit();
}

$urn = $data['urn'];
$date = date('Y-m-d'); // Current date

try {
  // Check if the student exists
  $stmt = $pdo->prepare("SELECT name FROM Students WHERE urn = :urn AND deleted = 0");
  $stmt->execute(['urn' => $urn]);
  $student = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$student) {
    echo json_encode(['error' => 'Invalid QR code. Student not found.']);
    http_response_code(404); // Not Found
    exit();
  }

  // Mark the student as present (if not already marked)
  $attendanceStmt = $pdo->prepare("SELECT id FROM Attendance WHERE urn = :urn AND date = :date");
  $attendanceStmt->execute(['urn' => $urn, 'date' => $date]);
  $attendance = $attendanceStmt->fetch(PDO::FETCH_ASSOC);

  if (!$attendance) {
    // If no attendance record exists for this URN and date, insert one
    $insertStmt = $pdo->prepare("INSERT INTO Attendance (urn, date) VALUES (:urn, :date)");
    $insertStmt->execute(['urn' => $urn, 'date' => $date]);
  }

  // Return success message
  echo json_encode(['message' => 'Student marked as present!', 'name' => $student['name']]);
  http_response_code(200); // OK

} catch (PDOException $e) {
  echo json_encode(['error' => 'An error occurred while processing attendance.']);
  http_response_code(500); // Internal Server Error
}
