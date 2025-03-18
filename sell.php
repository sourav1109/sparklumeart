<?php
include('backend/config.php');
session_start();

// Fetch active products for the sell page
$productsStmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active'");
$productsStmt->execute();
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$cartItems = [];
$totalAmount = 0;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['username'] ?? 'Guest';

    // Fetch cart items for the logged-in user
    $cartStmt = $pdo->prepare("SELECT c.id as cart_id, p.id as product_id, p.name, p.price, c.quantity, p.images FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $cartStmt->execute([$userId]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

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
        .cart-container {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .review-section {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($userName ?? 'Guest'); ?>!</h2>

    <div class="products-container">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                
                <?php 
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
                
                <div class="review-section">
                    <h4>Reviews</h4>
                    <?php
                    $reviewStmt = $pdo->prepare("SELECT r.rating, r.comment, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ?");
                    $reviewStmt->execute([$product['id']]);
                    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong> <?php echo htmlspecialchars($review['comment']); ?> (Rating: <?php echo $review['rating']; ?>)</p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
                    
                    <?php if ($isLoggedIn): ?>
                        <button onclick="openReviewModal(<?php echo $product['id']; ?>)">Leave a Review</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products available at the moment.</p>
    <?php endif; ?>
    </div>

    <?php if ($isLoggedIn && !empty($cartItems)): ?>
        <div class="cart-container">
            <h2>Your Cart</h2>
            <ul>
                <?php foreach ($cartItems as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['name']); ?> - 
                        <?php echo htmlspecialchars($item['quantity']); ?> x $<?php echo htmlspecialchars($item['price']); ?> 
                        <form action="remove_from_cart.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total: $<?php echo $totalAmount; ?></strong></p>
            <a href="checkout.php">Proceed to Checkout</a>
        </div>
    <?php endif; ?>

    <div id="reviewModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.3);">
        <h3>Leave a Review</h3>
        <form id="reviewForm" method="POST" action="submit_review.php">
            <input type="hidden" name="product_id" id="modalProductId">
            <label>Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>
            <label>Comment:</label>
            <textarea name="comment" required></textarea>
            <button type="submit">Submit Review</button>
            <button type="button" onclick="closeReviewModal()">Cancel</button>
        </form>
    </div>

    <script>
    function openReviewModal(productId) {
        document.getElementById('modalProductId').value = productId;
        document.getElementById('reviewModal').style.display = 'block';
    }
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }
    </script>
</body>
</html>
