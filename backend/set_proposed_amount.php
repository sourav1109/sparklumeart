<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];
    $proposedAmount = $_POST['proposed_amount'];

    $stmt = $pdo->prepare("UPDATE uploads SET proposed_amount = ?, status = 'Awaiting User Approval' WHERE id = ?");
    $stmt->execute([$proposedAmount, $orderId]);

    header("Location: admin_dashboard.php?message=Proposed amount set successfully.");
    exit;
}
?>
