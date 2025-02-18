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
</head>
<body>
    <h1>Edit Product</h1>
    <form action="edit_product.php?id=<?= $id; ?>" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
        <label for="description">Description:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>
        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
