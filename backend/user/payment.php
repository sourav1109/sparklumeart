<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

if (isset($_GET['upload_id'])) {
    $uploadId = $_GET['upload_id'];
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
    $stmt->execute([$uploadId]);
    $upload = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$upload) {
        header("Location: ../../user/dashboard.php");
        exit;
    }

    $updateStmt = $pdo->prepare("UPDATE uploads SET status = 'Approved' WHERE id = ?");
    if ($updateStmt->execute([$uploadId])) {
        $userId = $_SESSION['user_id'];

        $orderCheckStmt = $pdo->prepare("
            SELECT COUNT(*) as order_count FROM uploads WHERE user_id = ? AND status = 'Approved'
        ");
        $orderCheckStmt->execute([$userId]);
        $orderCount = $orderCheckStmt->fetch(PDO::FETCH_ASSOC)['order_count'];

        $scoreUpdate = ($orderCount == 1) ? 10 : 5;

        $updateScoreStmt = $pdo->prepare("UPDATE users SET lead_score = lead_score + ? WHERE id = ?");
        $updateScoreStmt->execute([$scoreUpdate, $userId]);
    }
} else {
    header("Location: ../../user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1c1f2b, #252a3f);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .payment-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 420px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s;
        }
        .payment-card:hover {
            transform: scale(1.03);
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.4);
        }
        .form-select, .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .btn {
            background: linear-gradient(to right, #f39c12, #e67e22);
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background: linear-gradient(to right, #e67e22, #d35400);
            transform: scale(1.05);
        }
        .payment-icons i {
            font-size: 30px;
            margin: 10px;
            color: #f39c12;
            transition: transform 0.2s;
        }
        .payment-icons i:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <h2 class="mb-3">Confirm Your Payment</h2>
        <p class="fs-5"><strong>Amount:</strong> $<?php echo htmlspecialchars($upload['proposed_amount']); ?></p>

        <!-- Payment Icons -->
        <div class="payment-icons">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-paypal"></i>
            <i class="fas fa-university"></i>
        </div>

        <form action="confirm_payment.php" method="POST">
            <input type="hidden" name="upload_id" value="<?php echo $upload['id']; ?>">
            <label for="payment_method" class="form-label">Choose Payment Method:</label>
            <select class="form-select" name="payment_method" id="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
            <button type="submit" name="confirm_payment" class="btn mt-3">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>