<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h1>User Registration</h1>

    <div class="form-container">
        <form method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" id="phone" required>

            <label for="password">Password (min 6 characters):</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Register">
        </form>

        <?php
        // Function to validate phone number
        function isValidPhone($phone) {
            return preg_match("/^\d{10}$/", $phone); // 10-digit phone number
        }

        // Function to validate email format
        function isValidEmail($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        // Registration logic
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = trim($_POST['password']);

            $errors = [];

            // Validate email
            if (!isValidEmail($email)) {
                $errors[] = "Invalid email format.";
            }

            // Validate phone number
            if (!isValidPhone($phone)) {
                $errors[] = "Phone number must be exactly 10 digits.";
            }

            // Validate password length
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            }

            // If there are no errors, display a success message
            if (empty($errors)) {
                echo "<p class='success'>Registration successful! Welcome, $first_name.</p>";
                // Here you can insert the data into a database (e.g., MySQL)
            } else {
                // Display errors
                echo "<div class='error'>";
                foreach ($errors as $error) {
                    echo "<p>$error</p>";
                }
                echo "</div>";
            }
        }
        ?>

    </div>

</body>
</html>
