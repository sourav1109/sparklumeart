<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart System with Discount</title>
</head>
<body>
    <?php
    session_start();

    // Initialize the product list
    $products = [
        1 => ['name' => 'Product A', 'price' => 10.00],
        2 => ['name' => 'Product B', 'price' => 15.00],
        3 => ['name' => 'Product C', 'price' => 20.00],
        4 => ['name' => 'Product D', 'price' => 25.00]
    ];

    // Initialize the cart if not already set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add a product to the cart
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = ['quantity' => 1, 'price' => $products[$product_id]['price']];
        }
    }

    // Remove a product from the cart
    if (isset($_POST['remove_from_cart'])) {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    // Calculate the total cost and discount
    $total_cost = 0;
    foreach ($_SESSION['cart'] as $product_id => $details) {
        $total_cost += $details['quantity'] * $details['price'];
    }

    // Apply discount if total cost exceeds $100
    $discount = 0;
    if ($total_cost > 100) {
        $discount = $total_cost * 0.10; // 10% discount
        $total_cost_after_discount = $total_cost - $discount;
    } else {
        $total_cost_after_discount = $total_cost;
    }
    ?>

    <h2>Shopping Cart System with Discount</h2>

    <!-- Display Product List -->
    <h3>Available Products</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $id => $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>$<?= number_format($product['price'], 2) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?= $id ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Display Shopping Cart -->
    <h3>Your Cart</h3>
    <?php if (!empty($_SESSION['cart'])): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $product_id => $details): ?>
                <tr>
                    <td><?= htmlspecialchars($products[$product_id]['name']) ?></td>
                    <td><?= $details['quantity'] ?></td>
                    <td>$<?= number_format($details['price'], 2) ?></td>
                    <td>$<?= number_format($details['quantity'] * $details['price'], 2) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                            <button type="submit" name="remove_from_cart">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total Cost: $<?= number_format($total_cost, 2) ?></strong></p>
        <?php if ($discount > 0): ?>
            <p><strong>Discount (10%): $<?= number_format($discount, 2) ?></strong></p>
            <p><strong>Total Cost After Discount: $<?= number_format($total_cost_after_discount, 2) ?></strong></p>
        <?php endif; ?>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
