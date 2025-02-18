<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$ordersStmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
</head>
<body>
    <h1>All Orders</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']); ?></td>
                <td><?= htmlspecialchars($order['user_id']); ?></td>
                <td>$<?= htmlspecialchars($order['total_price']); ?></td>
                <td><?= htmlspecialchars($order['status']); ?></td>
                <td><?= htmlspecialchars($order['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
