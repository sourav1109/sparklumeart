<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'upload.php';
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Your Painting Request</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f3f8ff, #e6eefc);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #444;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .form-section {
            display: none;
            transition: opacity 0.5s ease;
        }

        .form-section.active {
            display: block;
        }

        label {
            display: block;
            font-size: 0.9rem;
            margin-top: 1rem;
            color: #555;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="file"],
        select {
            width: 100%;
            padding: 0.8rem;
            margin-top: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            background: #f1f8ff;
            font-size: 0.95rem;
            color: #333;
        }

        input:focus,
        select:focus {
            border-color: #6a8cf7;
            background: #eef6ff;
        }

        button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            width: 48%;
        }

        .next-btn {
            background: linear-gradient(135deg, #45aaf2, #6a8cf7);
            color: #fff;
            margin-top: 1.5rem;
        }

        .next-btn:hover {
            background: linear-gradient(135deg, #6a8cf7, #45aaf2);
        }

        .prev-btn {
            background: #f3f3f3;
            color: #555;
            margin-top: 1.5rem;
        }

        .prev-btn:hover {
            background: #dcdcdc;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #ddd;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background: linear-gradient(135deg, #6a8cf7, #45aaf2);
            width: 0;
            transition: width 0.3s ease;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Your Painting Request</h1>

        <div class="progress-bar">
            <div class="progress"></div>
        </div>

        <form action="backend/upload_process.php" method="POST" enctype="multipart/form-data">
            <!-- Form Sections -->
            <div class="form-section active">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Enter your name" required>
            </div>

            <div class="form-section">
                <label for="address">Address:</label>
                <input type="text" name="address" placeholder="Enter your address" required>
            </div>

            <div class="form-section">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <div class="form-section">
                <label for="phone">Phone:</label>
                <input type="tel" name="phone" placeholder="Enter your phone number" required>
            </div>

            <div class="form-section">
                <label for="page_size">Page Size:</label>
                <select name="page_size" required>
                    <option>A2</option>
                    <option>A3</option>
                    <option>A4</option>
                </select>
            </div>

            <div class="form-section">
                <label for="background_type">Background Type:</label>
                <select name="background_type" required>
                    <option>With Background</option>
                    <option>Without Background</option>
                </select>
            </div>

            <div class="form-section">
                <label for="paper_type">Paper Type:</label>
                <select name="paper_type" required>
                    <option>Normal Paper</option>
                    <option>Acid-Free Paper</option>
                </select>
                <label for="image">Upload Image:</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <!-- Navigation Buttons -->
            <div class="navigation-buttons">
                <button type="button" class="prev-btn" onclick="previousSection()" style="display: none;">Previous</button>
                <button type="button" class="next-btn" onclick="nextSection()">Next</button>
                <button type="submit" class="next-btn" style="display: none;">Submit</button>
            </div>
        </form>
    </div>

    <script>
        let currentSection = 0;
        const sections = document.querySelectorAll('.form-section');
        const nextButton = document.querySelector('.next-btn');
        const prevButton = document.querySelector('.prev-btn');
        const submitButton = document.querySelector('button[type="submit"]');
        const progress = document.querySelector('.progress');

        function showSection(index) {
            sections.forEach((section, i) => {
                section.classList.toggle('active', i === index);
            });
            updateProgressBar(index);
            prevButton.style.display = index > 0 ? 'inline-block' : 'none';
            nextButton.style.display = index < sections.length - 1 ? 'inline-block' : 'none';
            submitButton.style.display = index === sections.length - 1 ? 'inline-block' : 'none';
        }

        function nextSection() {
            if (currentSection < sections.length - 1) {
                currentSection++;
                showSection(currentSection);
            }
        }

        function previousSection() {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
            }
        }

        function updateProgressBar(sectionIndex) {
            const progressPercentage = ((sectionIndex + 1) / sections.length) * 100;
            progress.style.width = progressPercentage + '%';
        }

        document.addEventListener('DOMContentLoaded', () => {
            showSection(currentSection);
        });
    </script>
</body>
</html>
