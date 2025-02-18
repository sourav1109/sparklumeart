<?php

include('../config.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $product_key = uniqid('product_');

    // Handle image upload
    $imagePaths = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            // Generate a unique filename and move the file
            $imageName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
            $uploadDir = "../../assets/uploads/";
            $imagePath = $uploadDir . $imageName;

            // Create upload directory if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($tmp_name, $imagePath)) {
                // Save relative path for database storage
                $imagePaths[] = "assets/uploads/" . $imageName;
            } else {
                $_SESSION['error'] = "Failed to upload file: " . htmlspecialchars($_FILES['images']['name'][$key]);
                header("Location: manage_sell_page.php");
                exit;
            }
        }
    }

    // Convert image paths to JSON format for database storage
    $images = json_encode($imagePaths);

    try {
        // Insert product into the database
        $stmt = $pdo->prepare("INSERT INTO products (product_key, name, description, price, images, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$product_key, $name, $description, $price, $images]);

        $_SESSION['success'] = "Product added successfully!";
        header("Location: manage_sell_page.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding product: " . $e->getMessage();
        header("Location: manage_sell_page.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Artistic Theme</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #f3f4f6;
            color: #495057;
            position: relative;
        }

        /* Background Animation */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .brush-stroke {
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            filter: blur(40px);
            animation: moveBrushStrokes 10s infinite;
        }

        .brush-stroke:nth-child(1) {
            background: rgba(244, 143, 177, 0.8); /* Pink brush */
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .brush-stroke:nth-child(2) {
            background: rgba(129, 212, 250, 0.8); /* Light Blue brush */
            top: 60%;
            left: 25%;
            animation-delay: 2s;
        }

        .brush-stroke:nth-child(3) {
            background: rgba(255, 202, 40, 0.8); /* Yellow brush */
            top: 30%;
            left: 75%;
            animation-delay: 4s;
        }

        .brush-stroke:nth-child(4) {
            background: rgba(156, 204, 101, 0.8); /* Green brush */
            top: 70%;
            left: 85%;
            animation-delay: 6s;
        }

        .brush-stroke:nth-child(5) {
            background: rgba(186, 104, 200, 0.8); /* Purple brush */
            top: 50%;
            left: 50%;
            animation-delay: 8s;
        }

        @keyframes moveBrushStrokes {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            50% {
                transform: translateY(-50px) scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: translateY(50px) scale(1);
                opacity: 1;
            }
        }

        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            padding: 30px;
            margin: auto;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .form-container h1 {
            font-size: 2.4rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #f48fb1, #29b6f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 12px;
            background: #f3f4f6;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #29b6f6;
            box-shadow: 0 0 8px rgba(41, 182, 246, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #f48fb1, #29b6f6);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(244, 143, 177, 0.5);
        }

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            animation: fadeIn 1s forwards;
        }

        .message.error {
            background: rgba(255, 77, 77, 0.1);
            color: #e63946;
        }

        .message.success {
            background: rgba(38, 198, 218, 0.1);
            color: #26c6da;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="background">
        <div class="brush-stroke"></div>
        <div class="brush-stroke"></div>
        <div class="brush-stroke"></div>
        <div class="brush-stroke"></div>
        <div class="brush-stroke"></div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <h1>Add New Product</h1>

        <!-- Error and Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <label for="name">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" required>

            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product description" required></textarea>

            <label for="price">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter product price" required>

            <label for="images">Upload Images</label>
            <input type="file" class="form-control" id="images" name="images[]" multiple>

            <button type="submit">Add Product</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
