<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
</head>
<body>
    <form method="post">
        Enter product names (comma-separated): <input type="text" name="product_list" required>
        <button type="submit">Submit</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $products = explode(',', $_POST['product_list']);
        $products = array_map('trim', $products);
        sort($products);

        echo "<h3>Sorted Products:</h3>";
        foreach ($products as $product) {
            echo htmlspecialchars($product) . "<br>";
        }

        // Save to file
        $file = 'products.txt';
        if (file_exists($file)) {
            $existing = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $products = array_unique(array_merge($existing, $products));
        }
        file_put_contents($file, implode(PHP_EOL, $products) . PHP_EOL);
        echo "<p>Products saved to 'products.txt'.</p>";
    }
    ?>
</body>
</html>
