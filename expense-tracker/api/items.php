<?php
require_once __DIR__ . '/init.php';

if (isset($_GET['month'])) {
    $month = (int)$_GET['month'];

    try {
        $stmt = $conn->prepare("SELECT * FROM expenses WHERE month = :month ORDER BY createdAt");
        $stmt->execute(['month' => $month]);

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalAmount = 0;
        foreach ($expenses as $expense) {
            $totalAmount += $expense['amount'];
        }

        echo json_encode([
            'expenses' => $expenses,
            'totalAmount' => $totalAmount
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    try {
        $stmt = $pdo->query("SELECT DISTINCT month FROM expenses ORDER BY month");

        $months = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['months' => $months]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>