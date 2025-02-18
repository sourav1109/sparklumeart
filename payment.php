<?php
session_start();
include('../config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Retrieve order_id from query string
$order_id = $_GET['order_id'] ?? null;
$user_id = $_SESSION['user_id'];

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    // Redirect if the order is not found or doesn't belong to the logged-in user
    echo "Order not found.";
    exit;
}

// Decode product details from JSON
$cartItems = json_decode($order['product_details'], true);
$totalPrice = $order['total_price'];

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update order status to 'Confirmed'
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'Confirmed' WHERE order_id = ?");
    $stmt->execute([$order_id]);

    // Redirect to order summary page
    header("Location: ../../user_profile.php?order_status=confirmed");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            padding: 10px 0;
            color: #007bff;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Summary</h2>
    <p>Order ID: <?php echo htmlspecialchars($order_id); ?></p>

    <div class="cart-summary">
        <?php if ($cartItems): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</div>
                    <div>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>
        <div class="total">Total: $<?php echo number_format($totalPrice, 2); ?></div>
    </div>

    <form method="POST" action="">
        <button type="submit">Confirm Payment</button>
    </form>
</div>

</body>
</html>
