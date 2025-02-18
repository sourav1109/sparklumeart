<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_id = $_POST['upload_id'];

    $stmt = $pdo->prepare("UPDATE uploads SET status = 'Delivered', delivery_confirmation = NOW() WHERE id = ?");
    $stmt->execute([$upload_id]);

    header("Location: dashboard.php?message=Delivery confirmed.");
    exit;
}
?>
