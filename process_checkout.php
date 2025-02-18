<?php
include('backend/config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $totalAmount = $_POST['total_amount'];

    // Save order to the database
    $orderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, name, email, phone, address, total_amount, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $orderStmt->execute([$userId, $name, $email, $phone, $address, $totalAmount]);

    // Send confirmation email
    $to = $email;
    $subject = "Order Confirmation";
    $message = "Thank you for your order, $name! Your total amount is $$totalAmount.";
    $headers = "From: no-reply@example.com";

    mail($to, $subject, $message, $headers);

    // Redirect to a success page
    header('Location: order_success.php');
    exit();
}
?>
