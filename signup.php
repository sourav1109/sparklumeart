<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <h1>Create Your Account</h1>
        <p>Sign up to get started</p>
    </header>
    
    <div class="container">
        <!-- Display error message if user already exists or other issues occur -->
        <?php if (isset($_SESSION['signup_error'])): ?>
            <div class="error-message"><?php echo $_SESSION['signup_error']; unset($_SESSION['signup_error']); ?></div>
        <?php endif; ?>
        
        <!-- Display success message if OTP email was sent -->
        <?php if (isset($_SESSION['signup_success'])): ?>
            <div class="success-message"><?php echo $_SESSION['signup_success']; unset($_SESSION['signup_success']); ?></div>
        <?php endif; ?>
        
        <form action="backend/signup_process.php" method="POST" class="signup-form">
            <div class="input-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="signup-button">Sign Up</button>
            
            <p class="login-prompt">Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>

    <style>
        /* Style for the signup page */
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        header h1 {
            font-size: 24px;
            color: #333;
        }

        header p {
            color: #666;
            font-size: 14px;
        }

        .error-message {
            color: #ff4d4d;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success-message {
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .input-group i {
            margin-right: 8px;
            color: #666;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .signup-button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .signup-button:hover {
            background-color: #45a049;
        }

        .login-prompt {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-prompt a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-prompt a:hover {
            text-decoration: underline;
        }
    </style>
</body>
</html>
