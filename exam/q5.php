<!-- CREATE DATABASE IF NOT EXISTS feedback_db;

USE feedback_db;

CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->



<?php
// Database connection

$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "feedback_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert feedback
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO feedbacks (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $successMessage = "Feedback submitted successfully!";
        } else {
            $errorMessage = "Error submitting feedback: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
</head>
<body>
    <h1>Feedback Form</h1>

    <?php
    if (!empty($successMessage)) {
        echo "<p style='color: green;'>$successMessage</p>";
    }
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?>

    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br><br>

        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="5" required></textarea>
        <br><br>

        <button type="submit" name="submit">Submit Feedback</button>
    </form>

    <h2>Previous Feedbacks:</h2>
    <?php
    $result = $conn->query("SELECT name, email, message, submitted_at FROM feedbacks ORDER BY submitted_at DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($row["name"]) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($row["message"]) . "</p>";
            echo "<p><small><strong>Submitted at:</strong> " . $row["submitted_at"] . "</small></p>";
            echo "<hr>";
            echo "</div>";
        }
    } else {
        echo "<p>No feedback available.</p>";
    }
    $conn->close();
    ?>
</body>
</html>
