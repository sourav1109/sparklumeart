<?php
include('backend/config.php');


// Fetch active products for the sell page
$productsStmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active'");
$productsStmt->execute();
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC) ?? []; // Ensure $products is an array

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$cartItems = [];
$totalAmount = 0;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['username'] ?? 'Guest';

    // Fetch cart items for the logged-in user
    $cartStmt = $pdo->prepare("
        SELECT c.id as cart_id, p.id as product_id, p.name, p.price, c.quantity, p.images 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $cartStmt->execute([$userId]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC) ?? []; // Ensure $cartItems is an array

    // Calculate total cart amount
    foreach ($cartItems as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .products-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .product-card img {
            max-width: 100%;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .product-card h3 {
            color: #333;
            font-size: 1.2em;
        }
        .product-card p {
            color: #666;
        }
        .cart-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-top: 10px;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #218838;
        }
        .checkout-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            display: inline-block;
        }
        .checkout-button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin: 10px 0;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($userName ?? 'Guest'); ?>!</h2>

    <div class="products-container">
    <?php if (isset($_SESSION['cart_success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_SESSION['cart_success']); unset($_SESSION['cart_success']); ?></div>
    <?php elseif (isset($_SESSION['cart_error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_SESSION['cart_error']); unset($_SESSION['cart_error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>

                <?php 
                // Decode images and handle possible null or invalid JSON
                $images = json_decode($product['images'], true); 
                if (is_array($images) && !empty($images)): 
                ?>
                    <?php foreach ($images as $image): ?>
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Product Image">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No images available for this product.</p>
                <?php endif; ?>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <input type="number" name="quantity" min="1" value="1">
                    <?php if ($isLoggedIn): ?>
                        <button type="submit">Add to Cart</button>
                    <?php else: ?>
                        <a href="login.php">Login to Add to Cart</a>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products available at the moment.</p>
    <?php endif; ?>
</div>


    <?php if ($isLoggedIn && !empty($cartItems)): ?>
        <div class="cart-container">
            <h2>My Cart</h2>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p>Total: $<?php echo $item['price'] * $item['quantity']; ?></p>
                </div>
            <?php endforeach; ?>
            <h3>Total Amount: $<?php echo $totalAmount; ?></h3>
            <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        </div>
    <?php elseif ($isLoggedIn): ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
