<?php
// Include config file
require_once "backend/config.php";

try {
    // Fetch all images from the gallery
    $sql = "SELECT * FROM gallery";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $galleryImages = $stmt->fetchAll(); // Fetch all records as associative array
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage()); // Handle errors gracefully
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="assets/css/styles-gallery.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="gallery.php">Art Gallery</a></li>
                <li><a href="upload.php">Upload</a></li>
                <li><a href="sell.php">Art Shop</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </nav>
    </header>

    <div class="name">
        <h1>Sparklume</h1>
        <h2>Every Portrait, a Brilliant Flash with Spark Lume.</h2>
    </div>

    <section id="purpose">
        <div class="promo">
            <center>
                <p class="blink">40% OFF: Commission Your Affordable Portrait Sketch</p>
                <br>
                <a href="upload.html" class="upload-button">Upload Now</a>
                <h3 class="artists-gallery"><u>WELCOME TO OUR ART GALLERY</u></h3>
            </center>
        </div>
    </section>

    <div class="gallery">
        <?php if (!empty($galleryImages)): ?>
            <?php foreach ($galleryImages as $image): ?>
                <div class="image">
                    <img src="backend/uploads/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Gallery Image">
                    <div class="caption" style="text-align: center; color:black;">
                        <p><?php echo !empty($image['description']) ? htmlspecialchars($image['description']) : "No description available."; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No images found in the gallery.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for image preview -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <div class="modal-content">
            <img id="modalImage">
            <div id="modalCaption" class="caption"></div>
            <a id="prev" class="prev">&#10094;</a>
            <a id="next" class="next">&#10095;</a>
        </div>
    </div>

    <script src="assets/js/script-gallery.js"></script>

    <!-- Painter's Section -->
    <div class="painter-section">
        <div class="painter-picture">
            <img src="dipa.jpg" alt="Painter's Image" width="100%">
        </div>
        <div class="painter-message">
            <h2>Painter's Name</h2>
            <p>Immerse yourself in the captivating world of Dipanwita Kundu's artistry, where each brushstroke reveals a personal narrative. 
            As you explore the gallery, become part of our vibrant community of art enthusiasts. Every artwork is a portal into a symphony of stories, waiting to be uncovered.</p>
        </div>
    </div>

    <footer>
        <p style="text-align: center;font-size: 20px; padding: 10px;background-color: rgba(0,0,0,0.5);border: rgba(0,0,0,0.5);border-radius: 10px;color: blanchedalmond;">
            &copy; 2023 Your Website Name. All rights reserved.
        </p>
    </footer>
</body>
</html>
