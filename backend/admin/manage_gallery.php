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
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        .gallery-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .gallery-table th, .gallery-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .gallery-table th { background-color: #f4f4f4; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit { background-color: #007bff; }
        .btn-edit:hover { background-color: #0056b3; }
        .btn-delete { background-color: #dc3545; }
        .btn-delete:hover { background-color: #c82333; }
        .popup-form { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); }
        .popup-form input, .popup-form textarea, .popup-form button { width: 100%; margin-bottom: 10px; }
        .popup-form input[type="file"] { padding: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Gallery</h2>

    <!-- Display messages -->
    <?php if (isset($_SESSION['gallery_success'])): ?>
        <div style="color: green;"><?php echo $_SESSION['gallery_success']; unset($_SESSION['gallery_success']); ?></div>
    <?php elseif (isset($_SESSION['gallery_error'])): ?>
        <div style="color: red;"><?php echo $_SESSION['gallery_error']; unset($_SESSION['gallery_error']); ?></div>
    <?php endif; ?>

    <!-- Add New Gallery Form -->
    <form method="POST" enctype="multipart/form-data">
        <h3>Add New Gallery Item</h3>
        <label for="image">Image</label>
        <input type="file" name="image" id="image" required>
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3" required></textarea>
        <button type="submit" name="add_gallery" class="btn btn-edit">Add Gallery Item</button>
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
                    <td><img src="<?php echo $item['image_path']; ?>" alt="Gallery Image" width="100"></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-edit" onclick="showEditForm(<?php echo $item['id']; ?>)">Edit</button>
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

<!-- Edit Form Popup -->
<div id="edit-form" class="popup-form">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <label for="edit-image">Image</label>
        <input type="file" name="image" id="edit-image">
        <label for="edit-description">Description</label>
        <textarea name="description" id="edit-description" rows="3" required></textarea>
        <button type="submit" name="edit_gallery" class="btn btn-edit">Update</button>
    </form>
</div>

<script>
    function showEditForm(id) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-form').style.display = 'block';
    }
</script>
</body>
</html>
