<?php
// Start session to handle error messages
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../ABC/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 10px;
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        /* Error message styling */
        .error-message {
            color: #ff4d4d;
            background-color: #ffe6e6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
        }

        .input-group i {
            margin-right: 10px;
            color: #888;
        }

        .input-group input {
            border: none;
            outline: none;
            background: transparent;
            flex: 1;
            padding: 8px;
            font-size: 14px;
        }

        .login-button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .login-button:hover {
            background-color: #45a049;
        }

        .additional-options {
            margin-top: 10px;
            font-size: 14px;
        }

        .additional-options a {
            text-decoration: none;
            color: #007bff;
            transition: 0.3s;
        }

        .additional-options a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome Back!</h1>
        <p>Please login to your account</p>
        
        <!-- Error message placeholder -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-message"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <form action="backend/login_process.php" method="POST" class="login-form">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="login-button">Login</button>
            
            <div class="additional-options">
                <a href="forgot_password.php">Forgot Password?</a>
                <span> | </span>
                <a href="signup.php">Create an Account</a>
            </div>
        </form>
    </div>

    <!-- JavaScript validation -->
    <script>
        document.querySelector(".login-form").addEventListener("submit", function(event) {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!email || !password) {
                alert("Please fill out both email and password fields.");
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>
</html>