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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin: 15px 0;
            text-decoration: none;
            color: #fff;
            background: #28a745;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .product-img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }
        .action-btn {
            padding: 8px 12px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            margin-right: 5px;
            display: inline-block;
            font-size: 14px;
        }
        .edit { background: #ffc107; }
        .delete { background: #dc3545; }
        .status { background: #17a2b8; }
        .edit:hover { background: #e0a800; }
        .delete:hover { background: #c82333; }
        .status:hover { background: #138496; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        <a href="add_product.php" class="btn">Add New Product</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
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
                    <td>
    <?php 
    $images = json_decode($product['images'], true); // Decode JSON array
    $imagePath = !empty($images) ? htmlspecialchars($images[0]) : 'assets/uploads/default.png'; // Use first image or default
    ?>
    <img src="../../<?= $imagePath; ?>" alt="Product Image" class="product-img" style="width: 100px; height: 100px;">
</td>

                    <td><?= htmlspecialchars($product['product_key']); ?></td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td>$<?= htmlspecialchars($product['price']); ?></td>
                    <td><?= htmlspecialchars($product['status']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id']; ?>" class="action-btn edit">Edit</a>
                        <a href="delete_product.php?id=<?= $product['id']; ?>" class="action-btn delete">Delete</a>
                        <a href="update_product_status.php?id=<?= $product['id']; ?>&status=<?= $product['status'] === 'active' ? 'inactive' : 'active'; ?>" 
                            class="action-btn status">
                            <?= $product['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
