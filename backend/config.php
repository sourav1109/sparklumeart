<?php
// Start session
session_start(); 

// Database configuration
$host = "localhost";                 // Database host
$dbname = "photo_selling_db";        // Database name
$username = "root";                  // Default MySQL username for XAMPP
$password = "";                      // Default MySQL password for XAMPP (usually empty)

$displayErrors = true;  // Set to false in production

try {
    if ($displayErrors) {
        // Enable PHP error reporting for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        // Log errors in production without displaying them to users
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/error.log'); // Save errors to error.log in the same directory as config.php
        ini_set('display_errors', 0);                 // Hide errors from displaying on the page
    }

    // Create a new PDO instance with UTF-8 character encoding
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);          // Enable exception handling for errors
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);     // Set default fetch mode to associative array

    // Debug message for successful database connection
    if ($displayErrors) {
        echo "Database connection successful.<br>"; // Remove this in production
    }

    // Set default timezone
    date_default_timezone_set('UTC'); // Set to 'UTC' or your preferred timezone

} catch (PDOException $e) {
    // Error handling with conditional message
    if ($displayErrors) {
        // Display detailed error message for development
        die("Database connection failed: " . $e->getMessage());
    } else {
        // Log error message for production
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

// Optional: Function to close the database connection
function closeConnection(&$pdo) {
    $pdo = null;  // Set PDO instance to null to close the connection
}

// Function to check if a user is logged in and has a specific role
function checkUserRole($role) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
        header("Location: ../login.php"); // Redirect to login if user is not logged in or does not have the correct role
        exit;
    }
}

// Utility function to sanitize inputs
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
