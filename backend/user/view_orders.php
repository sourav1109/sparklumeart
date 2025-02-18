<?php
session_start();
include('../config.php'); // Include database configuration

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$userId = $_SESSION['user_id']; // Get logged-in user's ID
$orders = [];
$orderItems = [];

try {
    // Fetch orders for the logged-in user
    $ordersStmt = $pdo->prepare("
        SELECT o.id AS order_id, o.total_price, o.status, o.created_at, 
               o.tentative_delivery_date, o.bill, o.cancellation_reason
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $ordersStmt->execute([$userId]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order items grouped by order_id
    $orderItemsStmt = $pdo->prepare("
        SELECT oi.order_id, p.name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN (SELECT id FROM orders WHERE user_id = ?)
    ");
    $orderItemsStmt->execute([$userId]);
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $_SESSION['order_fetch_error'] = "Failed to fetch your orders. Please try again later.";
}

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'] ?? null;
    $cancellationReason = $_POST['cancellation_reason'] ?? 'No reason provided';

    try {
        // Update order status to "cancelled"
        $cancelStmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'cancelled', cancellation_reason = ? 
            WHERE id = ? AND user_id = ? AND status = 'pending'
        ");
        $cancelStmt->execute([$cancellationReason, $orderId, $userId]);

        if ($cancelStmt->rowCount() > 0) {
            $_SESSION['order_cancel_success'] = "Order #$orderId has been cancelled successfully.";
        } else {
            $_SESSION['order_cancel_error'] = "Failed to cancel the order. It may already be processed.";
        }
    } catch (PDOException $e) {
        error_log("Error cancelling order: " . $e->getMessage());
        $_SESSION['order_cancel_error'] = "An error occurred while cancelling your order. Please try again later.";
    }

    header("Location: view_all_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .order-table th { background-color: #f4f4f4; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .btn-cancel { background-color: #dc3545; }
        .btn-cancel:hover { background-color: #c82333; }
        .btn-download { background-color: #007bff; }
        .btn-download:hover { background-color: #0056b3; }
        .popup-form { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); }
        .popup-form textarea, .popup-form button { width: 100%; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>My Orders</h2>

    <!-- Display messages -->
    <?php if (isset($_SESSION['order_cancel_success'])): ?>
        <div style="color: green;"><?php echo $_SESSION['order_cancel_success']; unset($_SESSION['order_cancel_success']); ?></div>
    <?php elseif (isset($_SESSION['order_cancel_error'])): ?>
        <div style="color: red;"><?php echo $_SESSION['order_cancel_error']; unset($_SESSION['order_cancel_error']); ?></div>
    <?php elseif (isset($_SESSION['order_fetch_error'])): ?>
        <div style="color: red;"><?php echo $_SESSION['order_fetch_error']; unset($_SESSION['order_fetch_error']); ?></div>
    <?php endif; ?>

    <!-- Orders Table -->
    <table class="order-table">
        <thead>
        <tr>
            <th>Order ID</th>
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
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo ucfirst($order['status']); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($order['status'] === 'approved' || $order['status'] === 'completed'): ?>
                                <?php if (!empty($order['bill'])): ?>
                                    <a href="<?php echo htmlspecialchars($order['bill']); ?>" class="btn btn-download" download>Download Bill</a>
                                <?php else: ?>
                                    <span style="color: gray;">Bill Not Available</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-cancel" onclick="showCancelForm(<?php echo $order['order_id']; ?>)">Cancel</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php if (isset($orderItems[$order['order_id']])): ?>
                    <tr>
                        <td colspan="5">
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
                <td colspan="5" style="text-align: center;">You have no orders.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Cancel Form Popup -->
<div id="cancel-form" class="popup-form">
    <form method="POST">
        <input type="hidden" name="order_id" id="cancel-order-id">
        <label for="cancellation_reason">Reason for Cancellation (Optional)</label>
        <textarea name="cancellation_reason" id="cancellation_reason" rows="3"></textarea>
        <button type="submit" name="cancel_order" class="btn btn-cancel">Submit</button>
    </form>
</div>

<script>
    function showCancelForm(orderId) {
        document.getElementById('cancel-order-id').value = orderId;
        document.getElementById('cancel-form').style.display = 'block';
    }
</script>
</body>
</html>
