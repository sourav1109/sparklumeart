<?php
include('./backend/config.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: signup.php");
    exit;
}

$email = $_SESSION['email'];
$message = "";

// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);

    $stmt = $pdo->prepare("SELECT * FROM unverified_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp'] == $otp && strtotime($user['otp_expiry']) >= time()) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$user['username'], $user['email'], $user['password']]);

        $stmt = $pdo->prepare("DELETE FROM unverified_users WHERE email = ?");
        $stmt->execute([$email]);

        unset($_SESSION['email']);
        $_SESSION['signup_success'] = "Your account has been verified! You can now log in.";
        header("Location: ../abc/login.php");
        exit;
    } else {
        $message = "Invalid or expired OTP. Please try again.";
    }
}

// Handle OTP resend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    $stmt = $pdo->prepare("SELECT * FROM unverified_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user['otp_attempts'] < 2) {
        $otp = rand(100000, 999999);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $stmt = $pdo->prepare("UPDATE unverified_users SET otp = ?, otp_expiry = ?, otp_attempts = otp_attempts + 1 WHERE email = ?");
        $stmt->execute([$otp, $otp_expiry, $email]);

        $message = "OTP has been resent successfully.";
    } else {
        $message = "You have exceeded the maximum OTP resend attempts.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .message {
            color: red;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .resend {
            margin-top: 10px;
            background: #dc3545;
        }
        .resend:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify OTP</h1>
        <p>Please check your email for the OTP.</p>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="otp_verification.php" method="POST">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" name="verify_otp">Verify</button>
        </form>
        <form action="otp_verification.php" method="POST">
            <button type="submit" name="resend_otp" class="resend">Resend OTP</button>
        </form>
    </div>
</body>
</html>
