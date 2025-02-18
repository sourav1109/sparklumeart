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
        /* Inline CSS for error message styling */
        .error-message {
            color: #ff4d4d;
            background-color: #ffe6e6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        /* Center and style container */
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        
        /* Login form styling */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group label {
            font-size: 14px;
        }

        /* Error placeholder */
        .input-group input:invalid {
            border-color: #ff4d4d;
        }

        /* Button styling */
        .login-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome Back!</h1>
        <p>Please login to your account</p>
    </header>
    
    <div class="container">
        <!-- Error message placeholder -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-message"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <form action="backend/login_process.php" method="POST" class="login-form">
            <div class="input-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="login-button">Login</button>
            
            <div class="additional-options">
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                <span> | </span>
                <a href="signup.php" class="signup-link">Create an Account</a>
            </div>
        </form>
    </div>

    <!-- Optional JavaScript validation -->
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
