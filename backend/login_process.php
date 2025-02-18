<?php
session_start();
include('config.php'); // Include database configuration

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation to check if email and password are not empty
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email and password.";
        header("Location: ../login.php");
        exit;
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Invalid email format.";
        header("Location: ../login.php");
        exit;
    }

    // Fetch the user from the database based on the provided email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verify the password and check if the user exists
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set up session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email']; // Set email in session
        $_SESSION['role'] = $user['role'];   // Set role in the session

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header("Location: ../backend/admin/dashboard.php");
        } else {
            header("Location: ../backend/user/dashboard.php");
        }
        exit;
    } else {
        // Store error in session and redirect to login
        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: ../login.php");
        exit;
    }
} else {
    // Redirect to login if accessed directly
    header("Location: ../login.php");
    exit;
}
