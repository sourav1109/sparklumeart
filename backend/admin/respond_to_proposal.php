<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_id = $_POST['upload_id'];
    $response = $_POST['response'];

    if ($response === 'accept') {
        $status = 'Accepted - Awaiting Payment';
    } elseif ($response === 'decline') {
        $status = 'Cancelled';
    } else {
        header("Location: dashboard.php?error=Invalid response.");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE uploads SET status = ? WHERE id = ?");
    $stmt->execute([$status, $upload_id]);

    header("Location: dashboard.php?message=Response submitted.");
    exit;
}
?>
