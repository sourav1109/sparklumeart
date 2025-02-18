<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE uploads SET status = 'Delivered' WHERE id = ?");
    $stmt->execute([$orderId]);

    header("Location: user_dashboard.php?message=Delivery confirmed.");
    exit;
}
?>
