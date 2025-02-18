<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Name PHP</title> <!-- Replace "Your Name" with your actual name -->
</head>
<body>
    <h1>Your Name PHP</h1> <!-- Replace "Your Name" with your actual name -->

    <!-- ASCII Art -->
    <pre>
        A
       A A
      A   A
     AAAAAAA
    A       A
    A       A
    </pre>

    <?php
    // SHA256 Hash of Your Name
    echo "<p>SHA256 of my name Name: " . hash('sha256', 'Sourav') . "</p>"; 
    ?>
</body>
</html>
