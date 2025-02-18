<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .total {
            margin-top: 20px;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .discount {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Shopping Cart</h1>

    <?php
    // Define the ShoppingCart class
    class ShoppingCart {
        private $products = [];

        // Method to add a product to the cart
        public function addProduct($productName, $price, $quantity) {
            $this->products[] = [
                'productName' => $productName,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }

        // Method to calculate the total price
        public function calculateTotal() {
            $totalPrice = 0;
            foreach ($this->products as $product) {
                $totalPrice += $product['price'] * $product['quantity'];
            }
            return $totalPrice;
        }

        // Method to apply discount based on quantity
        public function applyDiscount($totalPrice) {
            if ($totalPrice > 500) {
                return $totalPrice * 0.90; // 10% discount for orders over 500
            }
            return $totalPrice;
        }

        // Method to display cart contents and total price
        public function displayCart() {
            if (empty($this->products)) {
                echo "<p>No products in the cart.</p>";
                return;
            }

            echo "<table>";
            echo "<tr><th>Product Name</th><th>Price</th><th>Quantity</th><th>Total</th></tr>";

            foreach ($this->products as $product) {
                $productTotal = $product['price'] * $product['quantity'];
                echo "<tr>";
                echo "<td>" . htmlspecialchars($product['productName']) . "</td>";
                echo "<td>" . number_format($product['price'], 2) . "</td>";
                echo "<td>" . $product['quantity'] . "</td>";
                echo "<td>" . number_format($productTotal, 2) . "</td>";
                echo "</tr>";
            }

            $totalPrice = $this->calculateTotal();
            $discountedPrice = $this->applyDiscount($totalPrice);

            echo "<tr><td colspan='3' style='text-align: right;'>Total Price:</td><td>" . number_format($totalPrice, 2) . "</td></tr>";
            if ($totalPrice > 500) {
                echo "<tr><td colspan='3' style='text-align: right;'>Discount (10%):</td><td class='discount'>- " . number_format($totalPrice - $discountedPrice, 2) . "</td></tr>";
            }
            echo "<tr><td colspan='3' style='text-align: right;'>Final Price:</td><td>" . number_format($discountedPrice, 2) . "</td></tr>";
            echo "</table>";
        }
    }

    // Create a ShoppingCart instance
    $cart = new ShoppingCart();

    // Add products to the cart
    $cart->addProduct("Product A", 100, 3); // Price: 100, Quantity: 3
    $cart->addProduct("Product B", 200, 2); // Price: 200, Quantity: 2
    $cart->addProduct("Product C", 50, 1);  // Price: 50, Quantity: 1

    // Display cart and total
    $cart->displayCart();
    ?>

</body>
</html>
