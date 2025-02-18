<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];
    $tentativeDate = $_POST['tentative_date'];

    $stmt = $pdo->prepare("UPDATE uploads SET tentative_date = ?, status = 'Tentative Date Set' WHERE id = ?");
    $stmt->execute([$tentativeDate, $orderId]);

    header("Location: admin_dashboard.php?message=Tentative date set successfully.");
    exit;
}
?>
