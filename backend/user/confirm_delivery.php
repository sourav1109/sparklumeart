<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = $_POST['order_id'];

// Update order status to "Delivered" by user confirmation
$stmt = $pdo->prepare("UPDATE uploads SET status = 'Delivered', is_active = 0 WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);

echo "Order delivery confirmed successfully!";
?>
