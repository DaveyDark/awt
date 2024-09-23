<?php
// Database connection details
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "student_attendance";

try {
  // Create a new PDO instance with the database connection
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
  // Set PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Clear the tables before seeding
  $pdo->exec("DELETE FROM Attendance");
  $pdo->exec("DELETE FROM Students");
  $pdo->exec("DELETE FROM Admins");

  // Optional: Reset auto-increment values (if needed)
  $pdo->exec("ALTER TABLE Admins AUTO_INCREMENT = 1");
  $pdo->exec("ALTER TABLE Students AUTO_INCREMENT = 1");
  $pdo->exec("ALTER TABLE Attendance AUTO_INCREMENT = 1");

  // Define the admins to seed
  $admins = [
    ['name' => 'admin', 'password' => 'admin']
  ];

  // Seed Admins
  $adminStmt = $pdo->prepare("INSERT INTO Admins (name, password) VALUES (:name, :password)");

  foreach ($admins as $admin) {
    // Hash the password before inserting
    $hashedPassword = password_hash($admin['password'], PASSWORD_DEFAULT);

    // Bind the parameters and execute the statement
    $adminStmt->bindParam(':name', $admin['name']);
    $adminStmt->bindParam(':password', $hashedPassword);
    $adminStmt->execute();
  }

  // Define the students to seed
  $students = [
    ['urn' => '2203818', 'name' => 'Devesh', 'branch' => 'IT'],
    ['urn' => '2203810', 'name' => 'Avleen', 'branch' => 'IT'],
    ['urn' => '2203839', 'name' => 'Ishpreet', 'branch' => 'IT'],
    ['urn' => '2203855', 'name' => 'Mayank', 'branch' => 'IT']
  ];

  // Seed Students
  $studentStmt = $pdo->prepare("INSERT INTO Students (urn, name, branch) VALUES (:urn, :name, :branch)");

  foreach ($students as $student) {
    // Bind the parameters and execute the statement
    $studentStmt->bindParam(':urn', $student['urn']);
    $studentStmt->bindParam(':name', $student['name']);
    $studentStmt->bindParam(':branch', $student['branch']);
    $studentStmt->execute();
  }

  // Define attendance records to seed (URN, Date)
  $attendance = [
    ['urn' => '2203818', 'date' => '2024-09-23'],
    ['urn' => '2203818', 'date' => '2024-09-24'],
    ['urn' => '2203818', 'date' => '2024-09-25'],
    ['urn' => '2203818', 'date' => '2024-09-26'],
    ['urn' => '2203810', 'date' => '2024-09-23'],
    ['urn' => '2203810', 'date' => '2024-09-24'],
    ['urn' => '2203839', 'date' => '2024-09-25'],
    ['urn' => '2203855', 'date' => '2024-09-26']
  ];

  // Seed Attendance
  $attendanceStmt = $pdo->prepare("INSERT INTO Attendance (urn, date) VALUES (:urn, :date)");

  foreach ($attendance as $record) {
    // Bind the parameters and execute the statement
    $attendanceStmt->bindParam(':urn', $record['urn']);
    $attendanceStmt->bindParam(':date', $record['date']);
    $attendanceStmt->execute();
  }

  echo "Database successfully cleared and seeded with sample students and attendance\n";
} catch (PDOException $e) {
  echo "Seeding failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
