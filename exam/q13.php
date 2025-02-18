<!DOCTYPE html>
<html>
<head>
    <title>Event Registration System</title>
</head>
<body>
    <?php
    session_start();
    $conn = new mysqli('localhost', 'root', '', 'event_system');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle user registration
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "Registration successful. Please log in.<br>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }
    }

    // Handle user login
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo "Welcome, " . $user['name'] . "!<br>";
        } else {
            echo "Invalid email or password.<br>";
        }
    }

    // Handle event registration
    if (isset($_POST['register_event'])) {
        if (isset($_SESSION['user_id'])) {
            $event_id = $_POST['event_id'];
            $user_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $event_id);

            if ($stmt->execute()) {
                echo "You have successfully registered for the event!<br>";
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }
        } else {
            echo "Please log in to register for events.<br>";
        }
    }
    ?>

    <!-- User Registration Form -->
    <h3>User Registration</h3>
    <form method="post">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit" name="register">Register</button>
    </form>

    <!-- User Login Form -->
    <h3>User Login</h3>
    <form method="post">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit" name="login">Login</button>
    </form>

    <!-- Display Events -->
    <h3>Available Events</h3>
    <?php
    $result = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE()");
    if ($result->num_rows > 0) {
        while ($event = $result->fetch_assoc()) {
            echo "<p>";
            echo "<strong>" . $event['event_name'] . "</strong><br>";
            echo "Date: " . $event['event_date'] . "<br>";
            echo $event['event_description'] . "<br>";

            if (isset($_SESSION['user_id'])) {
                echo '<form method="post">';
                echo '<input type="hidden" name="event_id" value="' . $event['id'] . '">';
                echo '<button type="submit" name="register_event">Register</button>';
                echo '</form>';
            } else {
                echo "Please log in to register for this event.<br>";
            }

            echo "</p>";
        }
    } else {
        echo "No upcoming events.<br>";
    }
    ?>

    <!-- Logout -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="post" action="logout.php">
            <button type="submit" name="logout">Logout</button>
        </form>
    <?php endif; ?>

    <?php
    // For testing purposes: Add events manually
    if (isset($_POST['add_event'])) {
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];
        $event_description = $_POST['event_description'];

        $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $event_name, $event_date, $event_description);

        if ($stmt->execute()) {
            echo "Event added successfully.<br>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }
    }
    ?>

    <h3>Add Event (Admin Use Only)</h3>
    <form method="post">
        Event Name: <input type="text" name="event_name" required><br>
        Event Date: <input type="date" name="event_date" required><br>
        Event Description: <textarea name="event_description" required></textarea><br>
        <button type="submit" name="add_event">Add Event</button>
    </form>
</body>
</html>
