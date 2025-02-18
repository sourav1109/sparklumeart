<?php
include('config.php');
require '../vendor/autoload.php'; // Ensure PHPMailer is installed with Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    $existingUser = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM unverified_users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    $existingUnverifiedUser = $stmt->fetch();

    if ($existingUser || $existingUnverifiedUser) {
        $_SESSION['signup_error'] = "User with this email or username already exists!";
        header("Location: ../signup.php");
        exit;
    }

    // Generate OTP and set expiry
    $otp = rand(100000, 999999);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    // Insert data into `unverified_users` table with OTP and attempts set to 0
    $stmt = $pdo->prepare("INSERT INTO unverified_users (username, email, password, otp, otp_expiry, otp_attempts) VALUES (:username, :email, :password, :otp, :otp_expiry, 0)");
    $stmt->execute([
        'username' => $username, 
        'email' => $email, 
        'password' => $password, 
        'otp' => $otp, 
        'otp_expiry' => $otp_expiry
    ]);

    // Send OTP email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = 'sourav11092002@gmail.com'; // Your Gmail address
        $mail->Password   = 'bxzo cbna xukl lpmn'; // Your Gmail App Password (if 2FA enabled)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sourav092002@gmail.com', 'Sparklumeart');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code for Email Verification';
        $mail->Body    = "Hello $username,<br>Your OTP code is <strong>$otp</strong>.<br>Please enter this code within 5 minutes to verify your account.";

        $mail->send();

        // Redirect to OTP verification page
        $_SESSION['email'] = $email;
        $_SESSION['signup_success'] = "An OTP has been sent to your email. Please verify to complete your signup.";
        header("Location: ../otp_verification.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['signup_error'] = "Failed to send OTP email. Please try again later.";
        header("Location: ../signup.php");
        exit;
    }
} else {
    header("Location: ../signup.php");
    exit;
}
?>
