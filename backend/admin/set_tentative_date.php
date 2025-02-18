<?php
session_start();
include('../config.php');

// Ensure only admins can access this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if the form is submitted
if (isset($_POST['upload_id'], $_POST['tentative_date'])) {
    $uploadId = (int)$_POST['upload_id'];
    $tentativeDate = $_POST['tentative_date'];
    
    // Update the tentative date in the database
    $stmt = $pdo->prepare("UPDATE uploads SET tentative_date = ? WHERE id = ?");
    
    // Execute the update query
    if ($stmt->execute([$tentativeDate, $uploadId])) {
        // After the successful update, redirect back to the admin dashboard with a success message
        header("Location: admin_dashboard.php?message=DateSet");
        exit; // Always use exit after header redirect
    } else {
        // If there was an error, output an error message
        $_SESSION['error'] = "Failed to set the tentative date.";
        header("Location: admin_dashboard.php");
        exit;
    }
} else {
    // In case the request is invalid
    $_SESSION['error'] = "Invalid request.";
    header("Location: admin_dashboard.php");
    exit;
}
?>
