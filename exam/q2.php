<!-- Q2: Create a PHP program for an online art submission form with the required HTML and SQL.

SQL
CREATE DATABASE art_competition;

USE art_competition;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

-- Art submissions table
CREATE TABLE art_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    art_title VARCHAR(255) NOT NULL,
    art_file_path VARCHAR(255) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
create a upload folder
 -->
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "art_competition";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];

    $sql = "INSERT INTO users (full_name, email) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $full_name, $email);

    if ($stmt->execute()) {
        echo "<p>User registered successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle art submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_art"])) {
    $user_id = $_POST["user_id"];
    $art_title = $_POST["art_title"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["art_file"]["name"]);
    $upload_ok = 1;

    // Validate file type and size
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($file_type, ["png", "jpeg", "jpg", "gif"])) {
        echo "<p>Only PNG, JPEG, JPG, and GIF files are allowed.</p>";
        $upload_ok = 0;
    }
    if ($_FILES["art_file"]["size"] > 5 * 1024 * 1024) { // 5MB max
        echo "<p>File size must not exceed 5MB.</p>";
        $upload_ok = 0;
    }

    // Upload file and save submission
    if ($upload_ok && move_uploaded_file($_FILES["art_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO art_submissions (user_id, art_title, art_file_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $art_title, $target_file);

        if ($stmt->execute()) {
            echo "<p>Art submission uploaded successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>File upload failed.</p>";
    }
}

// Display art submissions
$sql = "SELECT u.full_name, s.art_title, s.art_file_path, s.submission_date 
        FROM art_submissions s 
        JOIN users u ON s.user_id = u.id
        ORDER BY s.submission_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Competition</title>
</head>
<body>
    <h1>Online Art Competition</h1>

    <!-- User Registration Form -->
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit" name="register">Register</button>
    </form>

    <!-- Art Submission Form -->
    <h2>Submit Your Art</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="user_id">User:</label>
        <select name="user_id" id="user_id" required>
            <?php
            // Fetch all users to populate the dropdown
            $user_query = "SELECT id, full_name FROM users";
            $users_result = $conn->query($user_query);
            while ($user = $users_result->fetch_assoc()) {
                echo "<option value='" . $user['id'] . "'>" . $user['full_name'] . "</option>";
            }
            ?>
        </select>
        <input type="text" name="art_title" placeholder="Art Title" required>
        <input type="file" name="art_file" accept=".png,.jpeg,.jpg,.gif" required>
        <button type="submit" name="submit_art">Submit Art</button>
    </form>

    <!-- Display Submitted Arts -->
    <h2>Submitted Arts</h2>
    <table border="1">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Art Title</th>
                <th>Art File</th>
                <th>Submission Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["full_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["art_title"]); ?></td>
                    <td><a href="<?php echo htmlspecialchars($row["art_file_path"]); ?>" target="_blank">View Art</a></td>
                    <td><?php echo htmlspecialchars($row["submission_date"]); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
