<?php
session_start();
if (!isset($_SESSION['order_success'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
</head>
<body>
    <div class="container">
        <h2>Order Placed Successfully</h2>
        <p><?php echo $_SESSION['order_success']; unset($_SESSION['order_success']); ?></p>
        <a href="index.php">Return to Homepage</a>
    </div>
</body>
</html>
