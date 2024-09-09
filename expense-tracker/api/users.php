<?php
session_start();
require_once "init.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit();
}

// Get the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];
$json = json_decode(file_get_contents("php://input"));

switch ($requestMethod) {
    case 'GET':
        // Fetch users
        try {
            $stmt = $pdo->prepare("SELECT id, name, type FROM Users WHERE deleted = 0");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
            http_response_code(200);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            http_response_code(500);
        }
        break;

    case 'POST':
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'register') {
                // Register new user
                if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['type'])) {
                    $name = $_POST['name'];
                    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
                    $type = $_POST['type']; // admin or user

                    try {
                        $stmt = $pdo->prepare("INSERT INTO Users (name, password, type, deleted) VALUES (:name, :password, :type, 0)");
                        $stmt->execute(['name' => $name, 'password' => $password, 'type' => $type]);
                        echo json_encode(['message' => 'User registered successfully!']);
                        http_response_code(201);
                    } catch (PDOException $e) {
                        echo json_encode(['error' => $e->getMessage()]);
                        http_response_code(500);
                    }
                } else {
                    echo json_encode(['error' => 'Please provide a name, password, and type.']);
                    http_response_code(400);
                }
            }
        }
        break;

    case 'PUT':
        // Update user details
        if (isset($json->id)) {
            $id = $json->id;
            $name = $json->name ?? null;
            $password = $json->password ?? null;
            $type = $json->type ?? null;

            try {
                // Check if user exists
                $userQuery = $pdo->prepare("SELECT * FROM Users WHERE id = :id AND deleted = 0");
                $userQuery->execute(['id' => $id]);
                $user = $userQuery->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    echo json_encode(['error' => 'User not found']);
                    http_response_code(404);
                    exit();
                }

                // Begin a transaction
                $pdo->beginTransaction();
                
                if ($name) {
                    $query = $pdo->prepare("UPDATE Users SET name = :name WHERE id = :id");
                    $query->execute(['name' => $name, 'id' => $id]);
                }
                
                if ($password) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    $query = $pdo->prepare("UPDATE Users SET password = :password WHERE id = :id");
                    $query->execute(['password' => $passwordHash, 'id' => $id]);
                }

                if ($type) {
                    $query = $pdo->prepare("UPDATE Users SET type = :type WHERE id = :id");
                    $query->execute(['type' => $type, 'id' => $id]);
                }

                // Add log
                $logQuery = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
                $logQuery->execute([
                    'user_id' => $_SESSION["user_id"],
                    'action' => "Updated user $id"
                ]);
                
                $pdo->commit();
                http_response_code(200);
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo json_encode(['error' => $e->getMessage()]);
                http_response_code(500);
            }
        } else {
            echo json_encode(['error' => 'User ID not provided.']);
            http_response_code(400);
        }
        break;

    case 'DELETE':
        // Delete user
        if (isset($json->id)) {
            $id = $json->id;

            try {
                // Check if user exists
                $userQuery = $pdo->prepare("SELECT * FROM Users WHERE id = :id AND deleted = 0");
                $userQuery->execute(['id' => $id]);
                $user = $userQuery->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    echo json_encode(['error' => 'User not found']);
                    http_response_code(404);
                    exit();
                }

                // Begin a transaction
                $pdo->beginTransaction();
                $query = $pdo->prepare("UPDATE Users SET deleted = 1 WHERE id = :id");
                $query->execute(['id' => $id]);

                // Add log
                $logQuery = $pdo->prepare("INSERT INTO Log (user_id, action) VALUES (:user_id, :action)");
                $logQuery->execute([
                    'user_id' => $_SESSION["user_id"],
                    'action' => "Deleted user $id"
                ]);

                $pdo->commit();
                http_response_code(204);
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo json_encode(['error' => $e->getMessage()]);
                http_response_code(500);
            }
        } else {
            echo json_encode(['error' => 'User ID not provided.']);
            http_response_code(400);
        }
        break;

    default:
        http_response_code(405);
}
?>
