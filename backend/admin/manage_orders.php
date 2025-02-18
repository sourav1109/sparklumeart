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

// Initialize $orders variable to avoid undefined variable warnings
$orders = [];
$orderItems = [];

// Fetch all orders and their items
try {
    $ordersStmt = $pdo->prepare("
        SELECT o.id AS order_id, o.user_id, o.total_price, o.status, o.created_at, 
               o.tentative_delivery_date, o.bill, o.cancellation_reason, o.phone, 
               u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $ordersStmt->execute();
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    $orderItemsStmt = $pdo->prepare("
        SELECT oi.order_id, p.name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
    ");
    $orderItemsStmt->execute();
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $_SESSION['order_fetch_error'] = "Failed to fetch orders. Please try again later.";
}

// Handle form submissions for approving orders, sending "Out for Delivery" emails, and marking orders as "Delivered"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;

    try {
        if (isset($_POST['confirm_order'])) {
            $tentativeDate = $_POST['tentative_date'] ?? null;
            $billFile = $_FILES['bill'] ?? null;

            if (!$tentativeDate || !$billFile) {
                throw new Exception("Tentative delivery date and bill file are required.");
            }

            if ($billFile['error'] === UPLOAD_ERR_OK) {
                $billPath = '../../backend/uploads/bills/' . basename($billFile['name']);
                move_uploaded_file($billFile['tmp_name'], $billPath);

                $updateStmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = 'approved', tentative_delivery_date = ?, bill = ? 
                    WHERE id = ?
                ");
                $updateStmt->execute([$tentativeDate, $billPath, $orderId]);

                // Fetch user details for the order
                $userStmt = $pdo->prepare("
                    SELECT u.email, u.username
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?
                ");
                $userStmt->execute([$orderId]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username   = 'sourav11092002@gmail.com'; // Replace with your Gmail address
                    $mail->Password   = 'bxzo cbna xukl lpmn'; // Replace with your Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('sourav11092002@gmail.com', 'Your Shop Name');
                    $mail->addAddress($user['email'], $user['username']);
                    $mail->isHTML(true);
                    $mail->Subject = "Order Approved - Order #$orderId";
                    $mail->Body = "
                        Dear {$user['username']},<br><br>
                        Your order #$orderId has been approved!<br>
                        Tentative Delivery Date: $tentativeDate<br><br>
                        Thank you for shopping with us!<br><br>
                        Regards,<br>Your Shop Name
                    ";

                    $mail->send();
                }
                $_SESSION['order_update_success'] = "Order #$orderId approved successfully.";
            } else {
                throw new Exception("Failed to upload the bill. Please try again.");
            }
        } elseif (isset($_POST['out_for_delivery'])) {
            // Send "Out for Delivery" email
            $userStmt = $pdo->prepare("
                SELECT u.email, u.username
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $userStmt->execute([$orderId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username   = 'sourav11092002@gmail.com';
                $mail->Password   = 'bxzo cbna xukl lpmn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('sourav11092002@gmail.com', 'Your Shop Name');
                $mail->addAddress($user['email'], $user['username']);
                $mail->isHTML(true);
                $mail->Subject = "Order Out for Delivery - Order #$orderId";
                $mail->Body = "
                    Dear {$user['username']},<br><br>
                    Your order #$orderId is out for delivery. It will be delivered today.<br><br>
                    Thank you for shopping with us!<br><br>
                    Regards,<br>Your Shop Name
                ";

                $mail->send();
                $_SESSION['order_update_success'] = "Out for delivery email sent for order #$orderId.";
            } else {
                throw new Exception("Failed to fetch user details for order #$orderId.");
            }
        } elseif (isset($_POST['delivered'])) {
            // Update order status to "completed"
            $updateStmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
            $updateStmt->execute([$orderId]);

            $userStmt = $pdo->prepare("
                SELECT u.email, u.username
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $userStmt->execute([$orderId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username   = 'sourav11092002@gmail.com';
                $mail->Password   = 'bxzo cbna xukl lpmn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('sourav11092002@gmail.com', 'Your Shop Name');
                $mail->addAddress($user['email'], $user['username']);
                $mail->isHTML(true);
                $mail->Subject = "Order Delivered - Order #$orderId";
                $mail->Body = "
                    Dear {$user['username']},<br><br>
                    Your order #$orderId has been delivered successfully.<br><br>
                    Thank you for shopping with us!<br><br>
                    Regards,<br>Your Shop Name
                ";

                $mail->send();
                $_SESSION['order_update_success'] = "Order #$orderId marked as delivered successfully.";
            } else {
                throw new Exception("Failed to fetch user details for order #$orderId.");
            }
        }
    } catch (Exception $e) {
        error_log("Error processing request: " . $e->getMessage());
        $_SESSION['order_update_error'] = $e->getMessage();
    }

    header("Location: manage_orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <style>
        /* CSS Styling */
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .order-table th { background-color: #f4f4f4; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { padding: 5px 10px; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .btn-approve { background-color: #28a745; }
        .btn-cancel { background-color: #dc3545; }
        .btn-delivery { background-color: #007bff; }
        .btn-delivered { background-color: #17a2b8; }
        .btn:hover { opacity: 0.9; }
        .popup-form { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); }
        .popup-form input, .popup-form textarea, .popup-form button { width: 100%; margin-bottom: 10px; }
        .popup-form input[type="file"] { padding: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Orders</h2>

    <!-- Display any messages -->
    <?php if (isset($_SESSION['order_fetch_error'])): ?>
        <div class="error-message" style="color: red;"><?php echo $_SESSION['order_fetch_error']; unset($_SESSION['order_fetch_error']); ?></div>
    <?php elseif (isset($_SESSION['order_update_success'])): ?>
        <div class="success-message" style="color: green;"><?php echo $_SESSION['order_update_success']; unset($_SESSION['order_update_success']); ?></div>
    <?php elseif (isset($_SESSION['order_update_error'])): ?>
        <div class="error-message" style="color: red;"><?php echo $_SESSION['order_update_error']; unset($_SESSION['order_update_error']); ?></div>
    <?php endif; ?>

    <table class="order-table">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo ucfirst($order['status']); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-approve" onclick="showApproveForm(<?php echo $order['order_id']; ?>)">Confirm</button>
                                <button class="btn btn-cancel" onclick="showCancelForm(<?php echo $order['order_id']; ?>)">Cancel</button>
                            <?php elseif ($order['status'] === 'approved'): ?>
                                <button class="btn btn-delivery" onclick="sendOutForDelivery(<?php echo $order['order_id']; ?>)">Out for Delivery</button>
                                <button class="btn btn-delivered" onclick="markAsDelivered(<?php echo $order['order_id']; ?>)">Delivered</button>
                            <?php elseif ($order['status'] === 'completed'): ?>
                                Delivered
                            <?php elseif ($order['status'] === 'cancelled'): ?>
                                Cancelled - Reason: <?php echo $order['cancellation_reason']; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php if (isset($orderItems[$order['order_id']])): ?>
                    <tr>
                        <td colspan="7">
                            <strong>Items:</strong>
                            <?php foreach ($orderItems[$order['order_id']] as $item): ?>
                                <p><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>) - $<?php echo number_format($item['price'], 2); ?></p>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">No orders found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Approve Form Popup -->
<div id="approve-form" class="popup-form">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="order_id" id="approve-order-id">
        <label for="tentative_date">Tentative Delivery Date</label>
        <input type="date" name="tentative_date" id="tentative-date" required>
        <label for="bill">Upload Bill</label>
        <input type="file" name="bill" id="bill" required>
        <button type="submit" name="confirm_order">Submit</button>
    </form>
</div>

<!-- Cancel Form Popup -->
<div id="cancel-form" class="popup-form">
    <form method="POST" action="update_order_status.php">
        <input type="hidden" name="order_id" id="cancel-order-id">
        <label for="reason">Reason for Cancellation</label>
        <textarea name="cancellation_reason" id="reason" rows="3" required></textarea>
        <button type="submit" name="cancel_order">Submit</button>
    </form>
</div>
<script>
    function showApproveForm(orderId) {
        document.getElementById('approve-order-id').value = orderId;
        document.getElementById('approve-form').style.display = 'block';
    }

    function showCancelForm(orderId) {
        document.getElementById('cancel-order-id').value = orderId;
        document.getElementById('cancel-form').style.display = 'block';
    }

    function sendOutForDelivery(orderId) {
        if (confirm("Send 'Out for Delivery' email?")) {
            fetch('manage_orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=${orderId}&out_for_delivery=1`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Hide the "Out for Delivery" button
                        document.querySelector(`.btn-delivery[data-order-id='${orderId}']`).style.display = 'none';
                    } else {
                        alert(data.message || "An error occurred while sending the email.");
                    }
                })
                .catch(err => alert("Error: " + err));
        }
    }

    function markAsDelivered(orderId) {
        if (confirm("Mark order as delivered?")) {
            fetch('manage_orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=${orderId}&delivered=1`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Hide the "Delivered" button
                        document.querySelector(`.btn-delivered[data-order-id='${orderId}']`).style.display = 'none';
                    } else {
                        alert(data.message || "An error occurred while updating the order status.");
                    }
                })
                .catch(err => alert("Error: " + err));
        }
    }
</script>

</body>
</html>
