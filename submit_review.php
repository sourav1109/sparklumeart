<?php
include('backend/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to submit a review.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Input validation
    if (empty($rating) || empty($comment)) {
        die("Error: All fields are required.");
    }
    if ($rating < 1 || $rating > 5) {
        die("Error: Rating must be between 1 and 5.");
    }

    // Insert review into the database
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $product_id, $rating, $comment])) {
        echo "<script>alert('Review submitted successfully!'); window.location.href='sell.php';</script>";
    } else {
        die("Error: Could not submit review.");
    }
} else {
    die("Invalid request.");
}
?>
