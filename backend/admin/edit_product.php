<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);

        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $id]);

        $_SESSION['success'] = "Product updated successfully!";
        header("Location: manage_sell_page.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 50%;
            background: #fff;
            padding: 30px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            text-align: center;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            margin-top: 15px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="edit_product.php?id=<?= $id; ?>" method="POST">
            <label for="name">Product Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
            
            <label for="description">Description:</label>
            <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>
            
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>
            
            <button type="submit" class="btn">Update Product</button>
        </form>
    </div>
</body>
</html>
