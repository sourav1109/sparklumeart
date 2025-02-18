<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_id = $_POST['upload_id'];
    $proposed_amount = $_POST['proposed_amount'];

    try {
        // Check if proposed_amount is not null
        if (!empty($proposed_amount)) {
            // Update the uploads table with proposed amount, status, and set price_updated flag to 1
            $stmt = $pdo->prepare("UPDATE uploads SET proposed_amount = ?, status = 'Awaiting User Response', price_updated = 1 WHERE id = ?");
            $stmt->execute([$proposed_amount, $upload_id]);

            header("Location: dashboard.php?message=Proposed amount set successfully.");
            exit;
        } else {
            echo "Proposed amount is required.";
        }
    } catch (PDOException $e) {
        echo "Error updating proposed amount: " . $e->getMessage();
    }
}
?>
