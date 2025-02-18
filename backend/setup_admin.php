<?php
// Include the database configuration file and start session
include('config.php');

require '../vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Admin email address
$adminEmail = "sourav11092002@gmail.com";

// Check if an admin already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
$stmt->execute();
$existingAdmin = $stmt->fetch();

if (!$existingAdmin) {
    // Generate a random password for the admin
    function generateRandomPassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    $adminUsername = 'admin';
    $adminPassword = generateRandomPassword(); // Generate a random password
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT); // Hash the password

    // Insert the admin user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$adminUsername, $adminEmail, $hashedPassword]);

    // Set up PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings for Gmail SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sourav11092002@gmail.com'; // Your Gmail address
        $mail->Password   = 'bxzo cbna xukl lpmn'; // Your Gmail App Password (if 2FA enabled)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // SMTP port for TLS

        // Recipients
        $mail->setFrom('sourav11092002@gmail.com', 'Your App Name'); // Use your Gmail address as the sender
        $mail->addAddress($adminEmail); // Add recipient (admin email)

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Your Admin Account Credentials';
        $mail->Body    = "Hello,<br><br>Your admin account has been created.<br><br><strong>Username:</strong> $adminUsername<br><strong>Password:</strong> $adminPassword<br><br>Please change your password after logging in for security.<br><br>Thank you!";

        // Send the email
        $mail->send();
        echo "Admin account created and credentials sent to $adminEmail.";
    } catch (Exception $e) {
        echo "Failed to send email. Admin account created with username '$adminUsername' and generated password. Please contact support.";
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Admin account already exists. No new admin account created.";
}
?>
