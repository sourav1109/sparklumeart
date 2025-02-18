<?php
session_start();
include('../config.php');

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

// Check if the request method is POST and required parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_id'], $_POST['payment_method'])) {
    $uploadId = (int)$_POST['upload_id']; // Cast upload_id to integer for security
    $paymentMethod = $_POST['payment_method'];

    // Fetch the upload to verify the data exists
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
    $stmt->execute([$uploadId]);
    $upload = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($upload) {
        try {
            // Update status to 'Approved' and record the payment method
            $updateStmt = $pdo->prepare("UPDATE uploads SET status = 'Approved', payment_method = ? WHERE id = ?");
            $updateStmt->execute([$paymentMethod, $uploadId]);

            // Use a session variable to store confirmation message for display on the dashboard
            $_SESSION['payment_confirmation'] = "Your payment has been successfully recorded, and the status is now Approved.";

            // Redirect to the user dashboard with a confirmation message
            header("Location: dashboard.php"); // Updated path to go directly to user dashboard
            exit;

        } catch (PDOException $e) {
            // Log error or handle it as needed
            error_log("Error updating payment: " . $e->getMessage());

            // Redirect to dashboard with error message
            $_SESSION['payment_error'] = "An error occurred while processing your payment. Please try again.";
            header("Location: dashboard.php"); // Updated path to go directly to user dashboard
            exit;
        }

    } else {
        // Redirect if upload not found with an error message
        $_SESSION['payment_error'] = "Upload not found. Please try again.";
        header("Location: dashboard.php"); // Updated path to go directly to user dashboard
        exit;
    }
} else {
    // Redirect if invalid request with an error message
    $_SESSION['payment_error'] = "Invalid request. Please try again.";
    header("Location: dashboard.php"); // Updated path to go directly to user dashboard
    exit;
}
?>
