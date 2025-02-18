<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$requestId = $_POST['request_id'];
$response = $_POST['response'];

if ($response === 'accept') {
    $stmt = $pdo->prepare("UPDATE uploads SET status = 'Accepted by User' WHERE id = ? AND user_id = ?");
    $stmt->execute([$requestId, $userId]);
    header("Location: payment.php?request_id=$requestId");  // Redirect to payment page
} elseif ($response === 'decline') {
    $stmt = $pdo->prepare("UPDATE uploads SET status = 'Canceled by User' WHERE id = ? AND user_id = ?");
    $stmt->execute([$requestId, $userId]);
    header("Location: user_dashboard.php");
}
exit;
?>
