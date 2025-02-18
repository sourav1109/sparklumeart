<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $fitness_goal = $_POST["fitness_goal"];

    $sql = "INSERT INTO users (username, email, password, fitness_goal) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $fitness_goal);

    if ($stmt->execute()) {
        echo "<p>User registered successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle user login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            echo "<p>Login successful! Welcome, $username.</p>";
        } else {
            echo "<p>Invalid password.</p>";
        }
    } else {
        echo "<p>User not found.</p>";
    }
    $stmt->close();
}

// Handle logging meals
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["log_meal"])) {
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $meal_name = $_POST["meal_name"];
        $calorie_count = $_POST["calorie_count"];

        $sql = "INSERT INTO meals (user_id, meal_name, calorie_count) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $user_id, $meal_name, $calorie_count);

        if ($stmt->execute()) {
            echo "<p>Meal logged successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error: You must be logged in to log a meal.</p>";
    }
}

// Display dashboard
$sql = "SELECT u.username, m.meal_name, m.calorie_count, m.logged_at
        FROM users u
        LEFT JOIN meals m ON u.id = m.user_id
        ORDER BY m.logged_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Challenge App</title>
</head>
<body>
    <h1>Fitness Challenge App</h1>

    <!-- User Registration Form -->
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="fitness_goal" required>
            <option value="Weight Loss">Weight Loss</option>
            <option value="Muscle Gain">Muscle Gain</option>
            <option value="Maintain Weight">Maintain Weight</option>
        </select>
        <button type="submit" name="register">Register</button>
    </form>

    <!-- User Login Form -->
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <!-- Log Meals Form -->
    <h2>Log a Meal</h2>
    <form method="POST">
        <input type="text" name="meal_name" placeholder="Meal Name" required>
        <input type="number" name="calorie_count" placeholder="Calorie Count" required>
        <button type="submit" name="log_meal">Log Meal</button>
    </form>

    <!-- Display Dashboard -->
    <h2>Dashboard</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Meal Name</th>
                <th>Calorie Count</th>
                <th>Logged At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["username"]); ?></td>
                    <td><?php echo htmlspecialchars($row["meal_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["calorie_count"]); ?></td>
                    <td><?php echo htmlspecialchars($row["logged_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>

<!-- 
-- Create database
CREATE DATABASE fitness_app;

-- Use the database
USE fitness_app;

-- Create table for users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fitness_goal ENUM('Weight Loss', 'Muscle Gain', 'Maintain Weight') NOT NULL,
    last_meal_logged DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table for meals
CREATE TABLE meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    meal_name VARCHAR(100) NOT NULL,
    calorie_count INT NOT NULL CHECK (calorie_count > 0),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
); -->
