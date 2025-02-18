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
    $otp = $_POST['otp'];

    // Fetch user data from `unverified_users`
    $stmt = $pdo->prepare("SELECT * FROM unverified_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp'] == $otp && strtotime($user['otp_expiry']) >= time()) {
        // Move data to `users` table
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$user['username'], $user['email'], $user['password']]);

        // Delete from `unverified_users`
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
    // Fetch user data from `unverified_users`
    $stmt = $pdo->prepare("SELECT * FROM unverified_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user['otp_attempts'] < 2) {
        $otp = rand(100000, 999999); // Generate new OTP
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Update OTP, expiry, and attempts count
        $stmt = $pdo->prepare("UPDATE unverified_users SET otp = ?, otp_expiry = ?, otp_attempts = otp_attempts + 1 WHERE email = ?");
        $stmt->execute([$otp, $otp_expiry, $email]);

        // Resend OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username   = 'sourav11092002@gmail.com'; // Your Gmail address
            $mail->Password   = 'bxzo cbna xukl lpmn'; // Your Gmail App Password (if 2FA enabled)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@example.com', 'Your App Name');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Resent OTP Code for Email Verification';
            $mail->Body    = "Hello {$user['username']},<br>Your new OTP code is <strong>$otp</strong>.<br>Please enter this code within 5 minutes to verify your account.";

            $mail->send();
            $message = "OTP has been resent successfully.";
        } catch (Exception $e) {
            $message = "Failed to resend OTP. Please try again later.";
        }
    } else {
        $message = "You have exceeded the maximum OTP resend attempts.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Verify OTP</h1>
        <p>Check your email for the OTP code.</p>
    </header>
    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="otp_verification.php" method="POST">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" name="verify_otp">Verify</button>
        </form>
        <form action="otp_verification.php" method="POST">
            <button type="submit" name="resend_otp">Resend OTP</button>
        </form>
    </div>
</body>
</html>
