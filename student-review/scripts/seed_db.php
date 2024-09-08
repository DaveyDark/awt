<?php
// Database connection details
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "student_review";

try {
  // Create a new PDO instance with the database connection
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
  // Set PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Clear the Users and Students tables before seeding
  $pdo->exec("DELETE FROM Users");
  $pdo->exec("DELETE FROM Students");

  // Optional: Reset auto-increment values (if needed)
  $pdo->exec("ALTER TABLE Users AUTO_INCREMENT = 1");
  $pdo->exec("ALTER TABLE Students AUTO_INCREMENT = 1");

  // Define the users to seed
  $users = [
    ['name' => 'dealinghand', 'password' => 'dealinghand', 'type' => 'l1'],
    ['name' => 'sectionincharge', 'password' => 'sectionincharge', 'type' => 'l2'],
    ['name' => 'deputyregistrar', 'password' => 'deputyregistrar', 'type' => 'l3'],
    ['name' => 'deanacademics', 'password' => 'deanacademics', 'type' => 'l4']
  ];

  // Seed Users
  $userStmt = $pdo->prepare("INSERT INTO Users (name, password, type) VALUES (:name, :password, :type)");

  foreach ($users as $user) {
    // Hash the password before inserting
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    // Bind the parameters and execute the statement
    $userStmt->bindParam(':name', $user['name']);
    $userStmt->bindParam(':password', $hashedPassword);
    $userStmt->bindParam(':type', $user['type']);
    $userStmt->execute();
  }

  // Define the students to seed with various levels of remarks
  $students = [
    ['name' => 'Devesh', 'urn' => '2203818', 'remark1' => NULL, 'remark2' => NULL, 'remark3' => NULL, 'remark4' => NULL, 'status' => 'pending'], // No remarks
    ['name' => 'Mayank', 'urn' => '2203855', 'remark1' => 'First warning issued', 'remark2' => NULL, 'remark3' => NULL, 'remark4' => NULL, 'status' => 'pending'], // Remark1 only
    ['name' => 'Avleen', 'urn' => '2203810', 'remark1' => 'First warning issued', 'remark2' => 'Second warning issued', 'remark3' => NULL, 'remark4' => NULL, 'status' => 'pending'], // Remark2 only
    ['name' => 'Ishpreet', 'urn' => '2203839', 'remark1' => 'First warning issued', 'remark2' => 'Second warning issued', 'remark3' => 'Final warning issued', 'remark4' => NULL, 'status' => 'pending'], // Remark3 only
    ['name' => 'Navkiran', 'urn' => '2203863', 'remark1' => 'First warning issued', 'remark2' => 'Second warning issued', 'remark3' => 'Final warning issued', 'remark4' => 'No action taken', 'status' => 'rejected'], // All remarks, status rejected (relieved)
    ['name' => 'SuspendedStudent', 'urn' => '2203865', 'remark1' => 'First warning issued', 'remark2' => 'Second warning issued', 'remark3' => 'Final warning issued', 'remark4' => 'Suspension confirmed', 'status' => 'accepted'] // All remarks, status accepted (suspended)
  ];

  // Seed Students
  $studentStmt = $pdo->prepare("INSERT INTO Students (name, urn, remark1, remark2, remark3, remark4, status) VALUES (:name, :urn, :remark1, :remark2, :remark3, :remark4, :status)");

  foreach ($students as $student) {
    // Bind the parameters and execute the statement
    $studentStmt->bindParam(':name', $student['name']);
    $studentStmt->bindParam(':urn', $student['urn']);
    $studentStmt->bindParam(':remark1', $student['remark1']);
    $studentStmt->bindParam(':remark2', $student['remark2']);
    $studentStmt->bindParam(':remark3', $student['remark3']);
    $studentStmt->bindParam(':remark4', $student['remark4']);
    $studentStmt->bindParam(':status', $student['status']);
    $studentStmt->execute();
  }

  echo "Database successfully cleared and seeded with users and students! \n";
} catch (PDOException $e) {
  echo "Seeding failed: " . $e->getMessage();
  http_response_code(500);
  exit();
}
