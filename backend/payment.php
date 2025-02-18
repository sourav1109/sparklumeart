<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$uploadId = $_GET['upload_id'];
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update status to Approved in the database after payment confirmation
    $stmt = $pdo->prepare("UPDATE uploads SET status = 'Approved', user_response = 'Accepted' WHERE id = ? AND user_id = ?");
    $stmt->execute([$uploadId, $userId]);

    // Show success message and redirect back to dashboard
    echo "<script>
            alert('Your payment is confirmed.');
            window.location.href = 'dashboard.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            text-align: center;
            padding: 50px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px;
            margin: auto;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Payment</h2>
        <p>Amount: $<?php echo htmlspecialchars($_GET['amount'] ?? ''); ?></p>
        <form method="POST">
            <button type="submit">Confirm Payment</button>
        </form>
    </div>
</body>
</html>
