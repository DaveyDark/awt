<?php
include __DIR__ . '/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $month = (int)($_POST['month'] ?? 0);

    if ($name && $amount && $month) {
        try {
            $stmt = $conn->prepare("INSERT INTO expenses (name, amount, month) VALUES (:name, :amount, :month)");
            $stmt->execute([
                'name' => $name,
                'amount' => $amount,
                'month' => $month
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'All fields (name, amount, month) are required']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>