<?php
session_start();
include('../config.php'); // Include database configuration

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Add new gallery item
        if (isset($_POST['add_gallery'])) {
            $description = $_POST['description'];
            $imageFile = $_FILES['image'];

            if ($imageFile['error'] === UPLOAD_ERR_OK) {
                $imagePath = '../../backend/uploads/gallery/' . basename($imageFile['name']);
                move_uploaded_file($imageFile['tmp_name'], $imagePath);

                $insertStmt = $pdo->prepare("INSERT INTO gallery (image_path, description) VALUES (?, ?)");
                $insertStmt->execute([$imagePath, $description]);
                $_SESSION['gallery_success'] = "Gallery item added successfully.";
            } else {
                $_SESSION['gallery_error'] = "Failed to upload the image. Please try again.";
            }
        }

        // Edit gallery item
        if (isset($_POST['edit_gallery'])) {
            $id = $_POST['id'];
            $description = $_POST['description'];
            $imageFile = $_FILES['image'];

            if (!empty($imageFile['name'])) {
                // If image is uploaded, update image and description
                if ($imageFile['error'] === UPLOAD_ERR_OK) {
                    $imagePath = '../../backend/uploads/gallery/' . basename($imageFile['name']);
                    move_uploaded_file($imageFile['tmp_name'], $imagePath);

                    $updateStmt = $pdo->prepare("UPDATE gallery SET image_path = ?, description = ? WHERE id = ?");
                    $updateStmt->execute([$imagePath, $description, $id]);
                } else {
                    $_SESSION['gallery_error'] = "Failed to upload the image. Please try again.";
                }
            } else {
                // Update only the description
                $updateStmt = $pdo->prepare("UPDATE gallery SET description = ? WHERE id = ?");
                $updateStmt->execute([$description, $id]);
            }

            $_SESSION['gallery_success'] = "Gallery item updated successfully.";
        }

        // Delete gallery item
        if (isset($_POST['delete_gallery'])) {
            $id = $_POST['id'];

            // Fetch the image path to delete the file
            $imageStmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
            $imageStmt->execute([$id]);
            $image = $imageStmt->fetch(PDO::FETCH_ASSOC);

            if ($image && file_exists($image['image_path'])) {
                unlink($image['image_path']); // Delete the image file
            }

            $deleteStmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $deleteStmt->execute([$id]);
            $_SESSION['gallery_success'] = "Gallery item deleted successfully.";
        }

    } catch (PDOException $e) {
        error_log("Error managing gallery: " . $e->getMessage());
        $_SESSION['gallery_error'] = "An error occurred while managing the gallery. Please try again.";
    }

    header("Location: manage_gallery.php");
    exit;
}

// Fetch all gallery items
$galleryItems = [];
try {
    $galleryStmt = $pdo->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
    $galleryStmt->execute();
    $galleryItems = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gallery items: " . $e->getMessage());
    $_SESSION['gallery_error'] = "Failed to fetch gallery items. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f8f9fa; 
            margin: 0; 
            padding: 40px; 
        }
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15); 
        }
        h2 { 
            text-align: center; 
            color: #333; 
        }
        form { 
            background: #f9f9f9; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            margin-bottom: 20px; 
        }
        form h3 { 
            margin-bottom: 15px; 
            color: #555; 
        }
        input, textarea, button { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 14px; 
        }
        textarea { 
            resize: none; 
        }
        button { 
            background: #007bff; 
            color: white; 
            border: none; 
            cursor: pointer; 
            transition: background 0.3s; 
        }
        button:hover { 
            background: #0056b3; 
        }
        .gallery-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        .gallery-table th, .gallery-table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
        }
        .gallery-table th { 
            background-color: #f4f4f4; 
            color: #333; 
        }
        .gallery-table tr:nth-child(even) { 
            background: #f9f9f9; 
        }
        .actions { 
            display: flex; 
            gap: 10px; 
        }
        .btn { 
            padding: 3px 5px; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: 0.3s; 
        }
        .btn-edit { 
            background-color: #28a745;
            padding: 2px 4px; 
        }
        .btn-edit:hover { 
            background-color: #218838; 
        }
        .btn-delete { 
            background-color: #dc3545; 
        }
        .btn-delete:hover { 
            background-color: #c82333; 
        }
        img { 
            width: 80px; 
            height: auto; 
            border-radius: 6px; 
        }

        /* Edit Form Popup */
        .overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.5); 
        }
        .popup-form { 
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2); 
            width: 400px; 
        }
        .popup-form h3 { 
            margin-bottom: 15px; 
            text-align: center; 
            color: #333; 
        }
        .close-btn { 
            background: #999; 
        }
        .close-btn:hover { 
            background: #777; 
        }
        .home-button {
        display: inline-flex;
        align-items: center;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        padding: 12px 20px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 8px;
        transition: 0.3s;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
    }

    .home-button:hover {
        background-color: #45a049;
        box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.3);
    }

    .home-button .icon {
        width: 24px;
        height: 24px;
        margin-right: 10px;
    }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Gallery</h2>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['gallery_success'])): ?>
        <div style="color: green; text-align:center;"><?php echo $_SESSION['gallery_success']; unset($_SESSION['gallery_success']); ?></div>
    <?php elseif (isset($_SESSION['gallery_error'])): ?>
        <div style="color: red; text-align:center;"><?php echo $_SESSION['gallery_error']; unset($_SESSION['gallery_error']); ?></div>
    <?php endif; ?>

    <!-- Add New Gallery Form -->
    <form method="POST" enctype="multipart/form-data">
        <h3>Add New Gallery Item</h3>
        <label for="image">Image</label>
        <input type="file" name="image" id="image" required>
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3" required></textarea>
        <button type="submit" name="add_gallery">Add Gallery Item</button>
    </form>

    <!-- Gallery Items Table -->
    <table class="gallery-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($galleryItems)): ?>
            <?php foreach ($galleryItems as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><img src="<?php echo $item['image_path']; ?>" alt="Gallery Image"></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-edit" onclick="showEditForm(<?php echo $item['id']; ?>, '<?php echo addslashes($item['description']); ?>')">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_gallery" class="btn btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No gallery items found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Overlay for Popup -->
<div class="overlay" id="overlay"></div>

<!-- Edit Form Popup -->
<div id="edit-form" class="popup-form">
    <h3>Edit Gallery Item</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <label for="edit-image">New Image (Optional)</label>
        <input type="file" name="image" id="edit-image">
        <label for="edit-description">Description</label>
        <textarea name="description" id="edit-description" rows="3" required></textarea>
        <button type="submit" name="edit_gallery">Update</button>
        <button type="button" class="btn close-btn" onclick="closeEditForm()">Cancel</button>
    </form>
</div>

<script>
    function showEditForm(id, description) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-form').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }
    function closeEditForm() {
        document.getElementById('edit-form').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }
</script>
<div style="text-align: center; margin: 20px;">
    <a href="dashboard.php" class="home-button">
        <i class="fas fa-tachometer-alt"></i> Back to dashboard
    </a>
</div>



</body>
</html>
