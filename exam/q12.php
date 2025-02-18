<!DOCTYPE html>
<html>
<head>
    <title>User Validation</title>
</head>
<body>
    <form method="post">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Security Question Answer: <input type="text" name="security_answer" required><br>
        <button type="submit">Submit</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $security_answer = $_POST['security_answer'];

        if (!$email) {
            echo "Invalid email format.<br>";
        } elseif (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            echo "Password must be at least 8 characters and include a special character.<br>";
        } elseif (empty($security_answer)) {
            echo "Security answer is required.<br>";
        } else {
            echo "Form submitted successfully.";
        }
    }
    ?>
</body>
</html>
