<?php
include('../config.php');

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // File upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imagePath = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/img/' . $imagePath);
        
        // Insert into gallery table
        $stmt = $pdo->prepare("INSERT INTO gallery (image_path, description, price) VALUES (?, ?, ?)");
        $stmt->execute([$imagePath, $description, $price]);
        echo "Image uploaded successfully!";
    } else {
        echo "Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Gallery</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Update Gallery</h1>
    </header>
    <div class="container">
        <form action="update_gallery.php" method="POST" enctype="multipart/form-data">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" required>
            <label for="image">Image:</label>
            <input type="file" name="image" id="image" required>
            <button type="submit">Upload Image</button>
        </form>
    </div>
</body>
</html>
