<?php
session_start();
include('../config.php');

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

if (isset($_GET['upload_id'])) {
    $uploadId = $_GET['upload_id'];

    // Fetch the upload to get details
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
    $stmt->execute([$uploadId]);
    $upload = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$upload) {
        // Redirect if upload not found
        header("Location: ../../user/dashboard.php");
        exit;
    }
} else {
    // Redirect if no upload ID is passed
    header("Location: ../../user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment for Painting Request</h1>
        <p><strong>Proposed Amount:</strong> $<?php echo htmlspecialchars($upload['proposed_amount']); ?></p>
        
        <!-- Payment form -->
        <form action="confirm_payment.php" method="POST">
            <input type="hidden" name="upload_id" value="<?php echo $upload['id']; ?>">
            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
            <br><br>
            <button type="submit" name="confirm_payment" value="Confirm Payment">Confirm Payment</button>
        </form>
    </div>
</body>
</html>
