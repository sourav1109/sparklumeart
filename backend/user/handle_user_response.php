<?php
session_start();
include('../config.php');

// Ensure the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Validate and sanitize inputs
if (!isset($_POST['upload_id'], $_POST['response'])) {
    // Redirect if necessary parameters are missing
    header("Location: dashboard.php");
    exit;
}

$uploadId = (int)$_POST['upload_id']; // Cast to an integer for safety
$response = $_POST['response'];

// Validate the response
if (!in_array($response, ['Accepted', 'Declined'])) {
    // If the response is invalid, redirect
    header("Location: dashboard.php");
    exit;
}

try {
    if ($response === 'Declined') {
        // Update status to Declined in the database
        $stmt = $pdo->prepare("UPDATE uploads SET status = 'Declined', user_response = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$response, $uploadId, $userId]);
    } elseif ($response === 'Accepted') {
        // Redirect to payment page with the upload ID
        header("Location: payment.php?upload_id=" . $uploadId);
        exit;
    }
    
    // After updating the response, redirect back to the dashboard
    header("Location: dashboard.php");
    exit;
} catch (PDOException $e) {
    // Handle any database errors
    echo "Error: " . $e->getMessage();
    exit;
}
?>
