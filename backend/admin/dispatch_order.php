<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bill_image'])) {
    $uploadId = $_POST['upload_id'];
    $billImage = $_FILES['bill_image'];

    // Ensure the upload is valid
    if ($billImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/bills/';
        $fileName = basename($billImage['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($billImage['tmp_name'], $filePath)) {
            $stmt = $pdo->prepare("UPDATE uploads SET bill_image = ?, status = 'Dispatched' WHERE id = ?");
            $stmt->execute([$filePath, $uploadId]);

            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file.";
    }
}
?>
