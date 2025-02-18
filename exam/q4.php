<!-- Q4: Scholarship Application Form with Validation using PHP and HTML.
 --><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application</title>
</head>
<body>
    <h1>Scholarship Application Form</h1>

    <form method="POST">
        <label for="fullName">Full Name:</label>
        <input type="text" name="fullName" id="fullName" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br><br>

        <label for="cgpa">CGPA:</label>
        <input type="number" name="cgpa" id="cgpa" step="0.01" required>
        <br><br>

        <label for="income">Annual Income (for Need-based only):</label>
        <input type="number" name="income" id="income" step="0.01">
        <br><br>

        <label for="scholarshipType">Scholarship Type:</label>
        <select name="scholarshipType" id="scholarshipType" required>
            <option value="Merit-based">Merit-based</option>
            <option value="Need-based">Need-based</option>
            <option value="Athletic">Athletic</option>
        </select>
        <br><br>

        <button type="submit" name="apply">Submit Application</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["apply"])) {
        $fullName = htmlspecialchars($_POST["fullName"]);
        $email = htmlspecialchars($_POST["email"]);
        $cgpa = floatval($_POST["cgpa"]);
        $income = isset($_POST["income"]) ? floatval($_POST["income"]) : null;
        $scholarshipType = $_POST["scholarshipType"];

        $errors = [];

        // Validate Name
        if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
            $errors[] = "Full Name must contain only alphabetic characters and spaces.";
        }

        // Validate CGPA based on Scholarship Type
        if ($scholarshipType === "Merit-based" && $cgpa <= 3.5) {
            $errors[] = "For Merit-based scholarships, CGPA must be greater than 3.5.";
        } elseif ($scholarshipType === "Need-based") {
            if ($income === null || $income <= 0) {
                $errors[] = "For Need-based scholarships, a valid income must be provided.";
            } elseif ($income >= 50000) {
                $errors[] = "For Need-based scholarships, annual income must be less than INR 50,000.";
            }
        }

        // Display Errors or Success
        if (!empty($errors)) {
            echo "<h3 style='color: red;'>Errors:</h3>";
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        } else {
            echo "<h3 style='color: green;'>Application Submitted Successfully!</h3>";
            echo "<p>Full Name: $fullName</p>";
            echo "<p>Email: $email</p>";
            echo "<p>CGPA: $cgpa</p>";
            if ($scholarshipType === "Need-based") {
                echo "<p>Annual Income: ₹$income</p>";
            }
            echo "<p>Scholarship Type: $scholarshipType</p>";
        }
    }
    ?>
</body>
</html>
