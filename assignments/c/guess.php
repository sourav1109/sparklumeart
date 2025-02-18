<?php
// Set a target number for the guessing game
$target = 65;

// Check if the 'guess' parameter is set in the URL
if (!isset($_GET['guess'])) {
    echo 'Missing guess parameter';
} else {
    // Get the 'guess' value from the URL query string
    $guess = $_GET['guess'];

    // Check if the guess is empty (too short)
    if (empty($guess)) {
        echo "Your guess is too short";
    }
    // Check if the guess is a number
    elseif (!is_numeric($guess)) {
        echo "Your guess is not a number";
    }
    // Check if the guess is too low
    elseif ($guess < $target) {
        echo "Your guess is too low";
    }
    // Check if the guess is too high
    elseif ($guess > $target) {
        echo "Your guess is too high";
    }
    // If the guess is correct
    else {
        echo "Congratulations - You are right!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>0ff982b6</title>
</head>
<body>
    <h1>Charles Severance PHP</h1>
    <p>Enter your guess:</p>
    <form method="get" action="">
        <input type="text" name="guess" placeholder="Your guess">
        <input type="submit" value="Submit">
    </form>

    <pre>
    CCCC  SSSS  PPPP  H   H
    C     S      P   P H   H
    C     SSSS   PPPP  HHHHH
    C     S      P     H   H
    CCCC  SSSS   P     H   H
    </pre>
</body>
</html>
