<?php
session_start();
include('config.php'); // Include database configuration
require '../vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in and if email exists in session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'upload.php';
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email']; // Retrieve email from session
$errors = [];

// Function to sanitize input fields
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Retrieve and sanitize form inputs
$name = sanitize_input($_POST['name'] ?? '');
$address = sanitize_input($_POST['address'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$page_size = sanitize_input($_POST['page_size'] ?? '');
$background_type = sanitize_input($_POST['background_type'] ?? '');
$paper_type = sanitize_input($_POST['paper_type'] ?? '');

// Check required fields
if (empty($name) || empty($address) || empty($phone) || empty($page_size) || empty($background_type) || empty($paper_type)) {
    $errors[] = "All fields are required.";
}

// Validate phone number (only digits and length check)
if (!preg_match('/^\d{10}$/', $phone)) {
    $errors[] = "Invalid phone number. Please enter a 10-digit number.";
}

// File validation and handling
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file_type = mime_content_type($_FILES['image']['tmp_name']);
    $file_size = $_FILES['image']['size'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file_type, $allowed_types)) {
        $errors[] = "Invalid file type. Only JPG, PNG, and GIF images are allowed.";
    }

    if ($file_size > 2 * 1024 * 1024) { // 2MB limit
        $errors[] = "File size exceeds the 2MB limit.";
    }

    if (empty($errors)) {
        $image_path = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $errors[] = "Failed to upload the image.";
        }
    }
} else {
    $errors[] = "Image upload is required.";
}

// If no validation errors, proceed with database insertion and email notification
if (empty($errors)) {
    try {
        // Insert data into the database using prepared statements
        $stmt = $pdo->prepare("INSERT INTO uploads (user_id, name, address, email, phone, image_path, page_size, background_type, paper_type, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending Proposal')");
        $stmt->execute([$user_id, $name, $address, $email, $phone, $image_path, $page_size, $background_type, $paper_type]);

        if ($stmt->rowCount() > 0) {
            // Email alert to admin using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $admin_email = "sourav092002@gmail.com"; // Replace with actual admin email
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username   = 'sourav11092002@gmail.com'; // Your Gmail address
                $mail->Password   = 'bxzo cbna xukl lpmn'; // Your Gmail App Password (if 2FA enabled)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Set up email content
                $mail->setFrom('your_email@gmail.com', 'Painting Requests'); // Replace with sender email and name
                $mail->addAddress($admin_email); // Admin email
                $mail->isHTML(true);
                $mail->Subject = "New Painting Request from User ID: $user_id";
                $mail->Body = "
                    <h2>New Painting Request Details</h2>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Address:</strong> $address</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Page Size:</strong> $page_size</p>
                    <p><strong>Background Type:</strong> $background_type</p>
                    <p><strong>Paper Type:</strong> $paper_type</p>
                    <p><strong>Image:</strong> <a href='https://yourwebsite.com/$image_path'>View Image</a></p>
                    <p>Status: Awaiting proposal response from user.</p>
                ";

                $mail->send();
                echo "Your request has been submitted successfully, and an alert has been sent to the admin.";

                // Update database to reflect pending proposal and payment status
                $orderId = $pdo->lastInsertId(); // Get the last inserted order ID for further actions

                // Insert additional entries in a separate table for tracking proposal and payment statuses
                $status_stmt = $pdo->prepare("INSERT INTO order_status (order_id, status, proposed_amount, tentative_date, dispatch_status, delivery_confirmation) VALUES (?, 'Pending Proposal', NULL, NULL, 'Not Dispatched', 'Awaiting Confirmation')");
                $status_stmt->execute([$orderId]);

            } catch (Exception $e) {
                echo "Request submitted, but email notification failed. Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Failed to save your request. Please try again.";
        }
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Database error: " . $e->getMessage());
        echo "An error occurred while saving your data. Please try again later.";
    }
} else {
    // Display validation errors
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>
