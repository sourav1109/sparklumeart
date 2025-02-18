<?php
session_start();
include('../config.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if upload ID and bill image are provided
if (isset($_POST['upload_id']) && isset($_FILES['bill_image'])) {
    $upload_id = $_POST['upload_id'];
    $bill_image = $_FILES['bill_image'];

    // Define the target directory for uploaded images
    $targetDir = "../uploads/bills/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);  // Create the directory if it doesn't exist
    }

    // Define the file path
    $targetFilePath = $targetDir . basename($bill_image['name']);
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Check file size (optional limit of 2MB)
    if ($bill_image['size'] > 2000000) {
        $_SESSION['error'] = "File is too large. Maximum size is 2MB.";
        header("Location: dashboard.php");
        exit;
    }

    // Allow only certain file formats (JPEG, PNG, GIF)
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $allowedTypes)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        header("Location: dashboard.php");
        exit;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($bill_image['tmp_name'], $targetFilePath)) {
        // Save the file path in the database
        $stmt = $pdo->prepare("UPDATE uploads SET bill_image = :bill_image, status = 'Dispatched' WHERE id = :upload_id");
        $stmt->execute([':bill_image' => $targetFilePath, ':upload_id' => $upload_id]);

        $_SESSION['message'] = "Bill image uploaded successfully and status updated to 'Dispatched'.";
    } else {
        $_SESSION['error'] = "There was an error uploading the file.";
    }
} else {
    $_SESSION['error'] = "Invalid request. Please try again.";
}

header("Location: dashboard.php");
exit;
?>
