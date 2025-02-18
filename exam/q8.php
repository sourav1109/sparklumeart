<!-- Gardening Community Registration, Login, "Remember Me", and Account Lock Features -->
<!-- -- Create the database
CREATE DATABASE gardening_community;

-- Use the database
USE gardening_community;

-- Create the 'users' table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    preferred_style VARCHAR(50) NOT NULL,
    failed_attempts INT DEFAULT 0,
    lock_time DATETIME DEFAULT NULL
);

-- Example Insert User (for testing)
INSERT INTO users (username, email, password, preferred_style) 
VALUES ('john_doe', 'john@example.com', '$2y$10$uV0V9yt1AtOTaW1bc1SmRu5eeh/7hplgWcswvAOwEDVHiC5N7XYnq', 'organic'); 
-- Password is hashed (you can use password_hash in PHP to hash a password) -->

<?php
// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gardening_community";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Secure password storage
    $preferred_style = $_POST["preferred_style"];

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, preferred_style, failed_attempts, lock_time) VALUES (?, ?, ?, ?, 0, NULL)");
    $stmt->bind_param("ssss", $username, $email, $password, $preferred_style);

    if ($stmt->execute()) {
        // Send welcome email
        $to = $email;
        $subject = "Welcome to Gardening Community!";
        $message = "Hello $username, welcome to the Gardening Community!";
        mail($to, $subject, $message);

        // Set a cookie for "Remember Me" feature (expires in 30 days)
        if (isset($_POST['remember'])) {
            setcookie("username", $username, time() + (86400 * 30), "/"); // Cookie expires in 30 days
        }

        echo "Registration successful! A welcome email has been sent.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];

    // Check for locked account
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if account is locked
        if ($user['failed_attempts'] >= 3 && time() - strtotime($user['lock_time']) < 900) {
            echo "Account locked. Please try again after 15 minutes.";
        } else {
            // Validate password
            if (password_verify($password, $user['password'])) {
                echo "Login successful!";
                // Reset failed attempts
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0 WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
            } else {
                // Increment failed attempts
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, lock_time = NOW() WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                echo "Invalid credentials. Attempt " . ($user['failed_attempts'] + 1) . "/3.";
            }
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gardening Community Registration and Login</title>
</head>
<body>

<h1>Gardening Community Registration</h1>
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <br><br>

    <label for="preferred_style">Preferred Gardening Style:</label>
    <select name="preferred_style" id="preferred_style">
        <option value="organic">Organic</option>
        <option value="hydroponics">Hydroponics</option>
        <option value="aeroponics">Aeroponics</option>
        <option value="traditional">Traditional</option>
    </select>
    <br><br>

    <label for="remember">Remember Me:</label>
    <input type="checkbox" name="remember" id="remember">
    <br><br>

    <button type="submit" name="register">Register</button>
</form>

<h1>Login to Gardening Community</h1>
<form method="POST">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <br><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>
