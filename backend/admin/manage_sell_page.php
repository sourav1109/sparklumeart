<?php

include('../config.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sell Page</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h1>Manage Products</h1>
    <a href="add_product.php" class="btn">Add New Product</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Key</th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']); ?></td>
                <td><?= htmlspecialchars($product['product_key']); ?></td>
                <td><?= htmlspecialchars($product['name']); ?></td>
                <td>$<?= htmlspecialchars($product['price']); ?></td>
                <td><?= htmlspecialchars($product['status']); ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $product['id']; ?>">Edit</a>
                    <a href="delete_product.php?id=<?= $product['id']; ?>">Delete</a>
                    <a href="update_product_status.php?id=<?= $product['id']; ?>&status=<?= $product['status'] === 'active' ? 'inactive' : 'active'; ?>">
                        <?= $product['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
