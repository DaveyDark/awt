<?php
session_start();
require_once "init.php";

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Check if user is logged in
            if (!isset($_SESSION["user_id"])) {
                http_response_code(401);
                exit();
            }
            // Return a list of all sheets that are not deleted
            $query = $pdo->prepare("SELECT id, name, timestamp FROM Sheets WHERE deleted = 0");
            $query->execute();
            echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
            http_response_code(200);
            break;

        case 'POST':
            // Creating a new sheet
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['name'])) {
                echo json_encode(['error' => 'Missing required data']);
                http_response_code(400);
                exit();
            }
            $sheet_name = $data['name'];

            try {
                // Begin transaction
                $pdo->beginTransaction();
                // Insert new sheet into the database
                $query = $pdo->prepare("INSERT INTO Sheets (name, deleted) VALUES (:name, 0)");
                $query->execute(['name' => $sheet_name]);

                // Get the last inserted sheet ID
                $newSheetId = $pdo->lastInsertId();

                // Commit transaction
                $pdo->commit();

                // Return new sheet data to the frontend
                echo json_encode(['id' => $newSheetId, 'name' => $sheet_name]);
                http_response_code(201);
            } catch (PDOException $e) {
                // Roll back the transaction in case of error
                $pdo->rollBack();
                echo json_encode(['error' => $e->getMessage()]);
                http_response_code(500);
            }
            break;

        case 'PUT':
            // Updating a sheet's name (optional implementation)
            break;

        case 'DELETE':
            // Soft-delete a sheet (optional implementation)
            break;

        default:
            http_response_code(405);
            break;
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
