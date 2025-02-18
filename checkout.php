<?php
session_start();
include('backend/config.php'); // Include database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php'; // Redirect back after login
    header("Location: login.php");
    exit;
}

// Fetch email from session
$userEmail = $_SESSION['email'] ?? ''; // Fallback to empty string if not set

// Fetch total amount from the cart for the logged-in user
try {
    $userId = $_SESSION['user_id'];
    $cartStmt = $pdo->prepare("
        SELECT SUM(c.quantity * p.price) AS total_amount
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $cartStmt->execute([$userId]);
    $cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

    $totalAmount = $cart['total_amount'] ?? 0; // Fallback to 0 if no cart items
} catch (PDOException $e) {
    error_log("Error fetching cart total: " . $e->getMessage());
    $totalAmount = 0;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect user input
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $pincode = htmlspecialchars(trim($_POST['pincode'] ?? ''));

    // Validate input
    if (empty($name)) {
        $_SESSION['checkout_error'] = "Please provide your name.";
    } elseif (empty($phone) || !preg_match('/^\d{10}$/', $phone)) {
        $_SESSION['checkout_error'] = "Please provide a valid 10-digit phone number.";
    } elseif (empty($address)) {
        $_SESSION['checkout_error'] = "Please provide a valid address.";
    } elseif (empty($pincode) || !preg_match('/^\d{6}$/', $pincode)) {
        $_SESSION['checkout_error'] = "Please provide a valid 6-digit pincode.";
    } elseif ($totalAmount <= 0) {
        $_SESSION['checkout_error'] = "Your cart is empty.";
    } else {
        try {
            // Insert order details into database with a status of "pending"
            $orderStmt = $pdo->prepare("
                INSERT INTO orders (user_id, email, name, phone, address, pincode, total_price, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $orderStmt->execute([
                $userId,
                $userEmail,
                $name,
                $phone,
                $address,
                $pincode,
                $totalAmount
            ]);

            // Save the inserted order ID for payment page
            $orderId = $pdo->lastInsertId();
            $_SESSION['order_id'] = $orderId;

            // Redirect to payment page
            header("Location: payment_sell.php?order_id=$orderId&total_amount=$totalAmount");
            exit;
        } catch (PDOException $e) {
            error_log("Error saving order: " . $e->getMessage());
            $_SESSION['checkout_error'] = "An error occurred while saving your order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea, button {
            margin-top: 5px;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
        .error-message {
            color: #ff4d4d;
            background-color: #ffe6e6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>

        <?php if (isset($_SESSION['checkout_error'])): ?>
            <div class="error-message"><?php echo $_SESSION['checkout_error']; unset($_SESSION['checkout_error']); ?></div>
        <?php endif; ?>

        <form action="checkout.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" placeholder="Enter your name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" disabled>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>

            <label for="address">Delivery Address:</label>
            <textarea name="address" id="address" rows="3" placeholder="Enter your delivery address" required></textarea>

            <label for="pincode">Pincode:</label>
            <input type="text" name="pincode" id="pincode" placeholder="Enter your 6-digit pincode" required>

            <label for="total">Total Amount:</label>
            <input type="text" id="total" value="$<?php echo number_format($totalAmount, 2); ?>" disabled>

            <button type="submit">Go to Payment Page</button>
        </form>
    </div>
</body>
</html>
