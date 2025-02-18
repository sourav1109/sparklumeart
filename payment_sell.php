<?php
session_start();
include('backend/config.php'); // Include the database configuration
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['email'];
$userName = $_SESSION['username'];

// Fetch total amount from the database (avoid relying on GET parameter)
try {
    $cartTotalStmt = $pdo->prepare("
        SELECT SUM(c.quantity * p.price) as total_amount 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $cartTotalStmt->execute([$userId]);
    $cart = $cartTotalStmt->fetch(PDO::FETCH_ASSOC);
    $totalAmount = $cart['total_amount'] ?? 0;

    if ($totalAmount <= 0) {
        $_SESSION['payment_error'] = "Your cart is empty or invalid total amount.";
        header("Location: checkout.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching cart total: " . $e->getMessage());
    $_SESSION['payment_error'] = "Unable to process the payment. Please try again.";
    header("Location: checkout.php");
    exit;
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert order into the database
        $orderStmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_price, status, created_at)
            VALUES (?, ?, 'paid', NOW())
        ");
        $orderStmt->execute([$userId, $totalAmount]);
        $orderId = $pdo->lastInsertId();

        // Fetch cart items
        $cartItemsStmt = $pdo->prepare("
            SELECT c.product_id, c.quantity, p.name, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $cartItemsStmt->execute([$userId]);
        $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            throw new Exception("Your cart is empty. Unable to process the order.");
        }

        // Insert cart items into `order_items`
        $orderItemsStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $productDetails = [];
        foreach ($cartItems as $item) {
            $orderItemsStmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
            $productDetails[] = $item['name'] . " (x" . $item['quantity'] . ")";
        }

        // Clear the user's cart
        $clearCartStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCartStmt->execute([$userId]);

        // Commit the transaction
        $pdo->commit();

        // Prepare email details
        $productList = implode(", ", $productDetails);
        $subject = "Order Confirmation - Order #$orderId";
        $message = "
            <h3>Hello $userName,</h3>
            <p>Thank you for your order! Your order has been successfully placed.</p>
            <p><strong>Order Details:</strong></p>
            <p><strong>Order ID:</strong> $orderId</p>
            <p><strong>Products:</strong> $productList</p>
            <p><strong>Total Amount:</strong> $$totalAmount</p>
            <p>We will notify you when your order is shipped. Thank you for shopping with us!</p>
            <p>Regards,<br>Your Shop Name</p>
        ";

        // Send confirmation email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sourav11092002@gmail.com'; // Your Gmail address
            $mail->Password = 'bxzo cbna xukl lpmn'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('sourav11092002@gmail.com', 'Your Shop Name');
            $mail->addAddress($userEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
        } catch (Exception $e) {
            error_log("Error sending email: " . $mail->ErrorInfo);
        }

        // Success alert and redirect to the dashboard
        $_SESSION['order_success'] = "Your order has been placed successfully!";
        echo "<script>
                alert('Thank you! Your order has been placed successfully. A confirmation email has been sent.');
                window.location.href = 'backend/user/dashboard.php';
              </script>";
        exit;

    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $pdo->rollBack();
        error_log("Error placing order: " . $e->getMessage());
        $errorMessage = $e->getMessage();

        // Error alert and redirect back to the payment page
        echo "<script>
                alert('Error placing the order: $errorMessage');
                window.location.href = 'payment_sell.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container h2 {
            color: #333;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Page</h2>
        <p>Total Amount: $<?php echo number_format($totalAmount, 2); ?></p>
        <form method="POST" action="payment_sell.php">
            <button type="submit" name="confirm_payment">Confirm Payment</button>
        </form>
    </div>
</body>
</html>
