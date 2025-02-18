<!-- A Simple CRUD Application for Products.
 CREATE DATABASE IF NOT EXISTS product_db;

USE product_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->


<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create Operation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add"])) {
    $name = htmlspecialchars($_POST["name"]);
    $description = htmlspecialchars($_POST["description"]);
    $price = htmlspecialchars($_POST["price"]);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $description, $price);

    if ($stmt->execute()) {
        $successMessage = "Product added successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Update Operation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $name = htmlspecialchars($_POST["name"]);
    $description = htmlspecialchars($_POST["description"]);
    $price = htmlspecialchars($_POST["price"]);

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $name, $description, $price, $id);

    if ($stmt->execute()) {
        $successMessage = "Product updated successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Delete Operation
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $successMessage = "Product deleted successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch products
$result = $conn->query("SELECT * FROM products");

// Don't close the connection here
// $conn->close(); <-- This line should be removed or placed at the end of the script after all database operations

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product CRUD</title>
</head>
<body>
    <h1>Product CRUD Application</h1>

    <?php if (isset($successMessage)) { echo "<p style='color: green;'>$successMessage</p>"; } ?>
    <?php if (isset($errorMessage)) { echo "<p style='color: red;'>$errorMessage</p>"; } ?>

    <h2>Add Product</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <br><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required></textarea>
        <br><br>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" required>
        <br><br>

        <button type="submit" name="add">Add Product</button>
    </form>

    <h2>Product List</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["description"]; ?></td>
                    <td><?php echo $row["price"]; ?></td>
                    <td>
                        <a href="q7.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="q7.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if (isset($_GET["edit"])): ?>
        <?php
        $editId = $_GET["edit"];
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $stmt->store_result();

        // Fixing bind_result with four fields to match the SELECT query
        $stmt->bind_result($id, $name, $description, $price);
        $stmt->fetch();
        ?>

        <h2>Edit Product</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $name; ?>" required>
            <br><br>

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required><?php echo $description; ?></textarea>
            <br><br>

            <label for="price">Price:</label>
            <input type="number" name="price" id="price" step="0.01" value="<?php echo $price; ?>" required>
            <br><br>

            <button type="submit" name="update">Update Product</button>
        </form>
    <?php endif; ?>

    <!-- Close connection here at the end of the script -->
    <?php $conn->close(); ?>
</body>
</html>
