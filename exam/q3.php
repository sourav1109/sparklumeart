<!-- Q3: Create a PHP script that calculates a user’s Body Mass Index (BMI), including the required HTML. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI Calculator</title>
</head>
<body>
    <h1>BMI Calculator</h1>

    <form method="POST">
        <label for="weight">Weight (kg):</label>
        <input type="number" name="weight" id="weight" step="0.1" required>
        <br><br>
        <label for="height">Height (m):</label>
        <input type="number" name="height" id="height" step="0.01" required>
        <br><br>
        <button type="submit" name="calculate">Calculate BMI</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["calculate"])) {
        $weight = $_POST["weight"];
        $height = $_POST["height"];

        if ($height > 0) {
            // Calculate BMI
            $bmi = $weight / ($height * $height);

            // Determine BMI category
            $category = "";
            if ($bmi < 18.5) {
                $category = "Underweight";
            } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
                $category = "Normal weight";
            } elseif ($bmi >= 25 && $bmi <= 29.9) {
                $category = "Overweight";
            } else {
                $category = "Obese";
            }

            // Display result
            echo "<h2>Result</h2>";
            echo "<p>Your BMI: " . number_format($bmi, 2) . "</p>";
            echo "<p>Category: $category</p>";
        } else {
            echo "<p style='color: red;'>Height must be greater than zero!</p>";
        }
    }
    ?>
</body>
</html>
