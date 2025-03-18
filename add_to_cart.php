<?php
session_start();
include('backend/config.php'); // Include database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Only users can add products to the cart!";
    $_SESSION['redirect_after_login'] = 'sell.php'; // Redirect back to sell page after login
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Check if the logged-in user is a customer (not an admin)
$roleCheckStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$roleCheckStmt->execute([$userId]);
$user = $roleCheckStmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'user') {
    $_SESSION['cart_error'] = "Only users can add products to the cart!";
    header("Location: sell.php");
    exit;
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id']; // Ensure product_id is an integer
    $quantity = (int)$_POST['quantity'];   // Ensure quantity is an integer

    // Validate input
    if ($productId <= 0 || $quantity <= 0) {
        $_SESSION['cart_error'] = "Invalid product or quantity.";
        header("Location: sell.php");
        exit;
    }

    try {
        // Check if the product exists and is active
        $productStmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
        $productStmt->execute([$productId]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            // Product not found or inactive
            $_SESSION['cart_error'] = "The selected product is not available.";
            header("Location: sell.php");
            exit;
        }

        // Check if the product is already in the cart
        $cartCheckStmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $cartCheckStmt->execute([$userId, $productId]);
        $cartItem = $cartCheckStmt->fetch(PDO::FETCH_ASSOC);

        if ($cartItem) {
            // Update the quantity if the product is already in the cart
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateCartStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $updateCartStmt->execute([$newQuantity, $cartItem['id']]);
        } else {
            // Insert the product into the cart
            $addCartStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $addCartStmt->execute([$userId, $productId, $quantity]);
        }

        // Success message
        $_SESSION['cart_success'] = "Product added to cart successfully!";
        header("Location: sell.php");
        exit;
    } catch (PDOException $e) {
        // Log and handle errors
        error_log("Error adding to cart: " . $e->getMessage());
        $_SESSION['cart_error'] = "An error occurred. Please try again.";
        header("Location: sell.php");
        exit;
    }
} else {
    // Invalid request
    $_SESSION['cart_error'] = "Invalid request.";
    header("Location: sell.php");
    exit;
}
?>
