<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];
    $targetDir = "../uploads/bills/";
    $billImage = $targetDir . basename($_FILES["bill_image"]["name"]);

    if (move_uploaded_file($_FILES["bill_image"]["tmp_name"], $billImage)) {
        $stmt = $pdo->prepare("UPDATE uploads SET status = 'Dispatched', bill_image = ? WHERE id = ?");
        $stmt->execute([$billImage, $orderId]);

        header("Location: admin_dashboard.php?message=Order dispatched successfully.");
        exit;
    } else {
        echo "Error uploading bill image.";
    }
}
?>
