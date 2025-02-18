<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    echo "Order ID not provided.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    echo "Order not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order</title>
</head>
<body>
    <h1>Order Details for ID: <?php echo htmlspecialchars($order['id']); ?></h1>
    <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
    <p><strong>Proposed Amount:</strong> <?php echo htmlspecialchars($order['proposed_amount']); ?></p>
    <p><strong>Tentative Date:</strong> <?php echo htmlspecialchars($order['tentative_date']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    <p><strong>Image:</strong> <img src="../<?php echo htmlspecialchars($order['image_path']); ?>" width="100"></p>
    <p><strong>Bill Image:</strong> <?php echo $order['bill_image'] ? "<img src='../{$order['bill_image']}' width='100'>" : "Not uploaded"; ?></p>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
