<?php
session_start();
include('../config.php'); // Include database configuration
require '../../vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Function to send emails
function sendEmail($to, $name, $subject, $message)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Your email
        $mail->Password = 'your_app_password'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('your_email@gmail.com', 'Your Shop Name');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Error sending email: " . $mail->ErrorInfo);
    }
}

// Handle POST requests for managing orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];

    try {
        if (isset($_POST['approve_order'])) {
            // Approve order with tentative delivery date and bill upload
            $tentativeDate = $_POST['tentative_date'];
            $billFile = $_FILES['bill'];

            if ($billFile['error'] === UPLOAD_ERR_OK) {
                $billPath = '../../backend/uploads/bills/' . basename($billFile['name']);
                move_uploaded_file($billFile['tmp_name'], $billPath);

                $updateStmt = $pdo->prepare("
                    UPDATE orders SET status = 'approved', tentative_delivery_date = ?, bill = ? WHERE id = ?
                ");
                $updateStmt->execute([$tentativeDate, $billPath, $orderId]);

                // Notify the user
                $userStmt = $pdo->prepare("SELECT u.email, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
                $userStmt->execute([$orderId]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    sendEmail(
                        $user['email'],
                        $user['username'],
                        "Order Approved",
                        "Dear {$user['username']},<br>Your order #$orderId has been approved.<br>Expected Delivery Date: $tentativeDate.<br>Thank you for shopping with us!"
                    );
                }
                $_SESSION['order_update_success'] = "Order approved successfully.";
            } else {
                $_SESSION['order_update_error'] = "Failed to upload the bill.";
            }
        } elseif (isset($_POST['cancel_order'])) {
            // Cancel the order
            $cancelReason = $_POST['cancellation_reason'] ?? 'No reason provided';
            $updateStmt = $pdo->prepare("
                UPDATE orders SET status = 'cancelled', cancellation_reason = ? WHERE id = ?
            ");
            $updateStmt->execute([$cancelReason, $orderId]);

            // Notify the user
            $userStmt = $pdo->prepare("SELECT u.email, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
            $userStmt->execute([$orderId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                sendEmail(
                    $user['email'],
                    $user['username'],
                    "Order Cancelled",
                    "Dear {$user['username']},<br>Your order #$orderId has been cancelled.<br>Reason: $cancelReason.<br>We apologize for any inconvenience caused."
                );
            }
            $_SESSION['order_update_success'] = "Order cancelled.";
        } elseif (isset($_POST['out_for_delivery'])) {
            // Mark as out for delivery
            $updateStmt = $pdo->prepare("UPDATE orders SET status = 'out_for_delivery' WHERE id = ?");
            $updateStmt->execute([$orderId]);

            // Notify the user
            $userStmt = $pdo->prepare("SELECT u.email, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
            $userStmt->execute([$orderId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                sendEmail(
                    $user['email'],
                    $user['username'],
                    "Out for Delivery",
                    "Dear {$user['username']},<br>Your order #$orderId is out for delivery and will arrive today.<br>Thank you for shopping with us!"
                );
            }
            $_SESSION['order_update_success'] = "Order marked as out for delivery.";
        } elseif (isset($_POST['delivered'])) {
            // Mark as delivered
            $updateStmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
            $updateStmt->execute([$orderId]);

            // Notify the user
            $userStmt = $pdo->prepare("SELECT u.email, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
            $userStmt->execute([$orderId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                sendEmail(
                    $user['email'],
                    $user['username'],
                    "Order Delivered",
                    "Dear {$user['username']},<br>Your order #$orderId has been successfully delivered.<br>We hope you enjoy your purchase!"
                );
            }
            $_SESSION['order_update_success'] = "Order marked as delivered.";
        }

        header("Location: manage_orders.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error updating order: " . $e->getMessage());
        $_SESSION['order_update_error'] = "An error occurred while updating the order.";
        header("Location: manage_orders.php");
        exit;
    }
}
?>
