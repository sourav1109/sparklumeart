<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .gallery img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        .message {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
        }
        .success {
            background-color: #ddffdd;
            color: #4caf50;
            border: 1px solid #4caf50;
        }
    </style>
</head>
<body>
    <h2>Simple Image Gallery System</h2>
    
    <?php
    // Directory to store uploaded images
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Error message placeholder
    $message = "";
    $messageType = ""; // Can be 'error' or 'success'

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Get file extension and validate it
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $message = "Invalid file type. Please upload a JPEG, PNG, or GIF file.";
            $messageType = "error";
        } elseif ($fileSize > 3 * 1024 * 1024) { // Maximum size: 3MB
            $message = "File size exceeds 3MB. Please upload a smaller image.";
            $messageType = "error";
        } elseif ($fileError !== 0) {
            $message = "An error occurred during the upload. Please try again.";
            $messageType = "error";
        } else {
            // Move file to uploads directory
            $newFileName = uniqid("img_", true) . "." . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                $message = "File uploaded successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to upload the file. Please check server permissions.";
                $messageType = "error";
            }
        }
    }

    // Fetch all uploaded images
    $images = array_diff(scandir($uploadDir), array('.', '..'));
    ?>

    <!-- Image Upload Form -->
    <form method="post" enctype="multipart/form-data">
        <label for="image">Upload Image (JPEG, PNG, GIF | Max: 3MB):</label><br><br>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload</button>
    </form>
    
    <?php if (!empty($message)): ?>
        <p class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <!-- Display Gallery -->
    <h3>Uploaded Images</h3>
    <div class="gallery">
        <?php if (empty($images)): ?>
            <p>No images uploaded yet. Upload some images to view them here!</p>
        <?php else: ?>
            <?php foreach ($images as $image): ?>
                <img src="<?= $uploadDir . $image ?>" alt="Uploaded Image">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
