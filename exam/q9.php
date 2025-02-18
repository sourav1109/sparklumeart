<?php
// Initialize the robot's state
$robot_state = [
    'position' => ['x' => 0, 'y' => 0], // Initial position at coordinates (0, 0)
    'direction' => 'north'              // Initial direction is 'north'
];

// Function to move the robot
function moveRobot(&$robot_state, $command) {
    switch ($command) {
        case 'move forward':
            // Move the robot forward based on its direction
            if ($robot_state['direction'] == 'north') {
                $robot_state['position']['y'] += 1; // Move north (increase y-coordinate)
            } elseif ($robot_state['direction'] == 'south') {
                $robot_state['position']['y'] -= 1; // Move south (decrease y-coordinate)
            } elseif ($robot_state['direction'] == 'east') {
                $robot_state['position']['x'] += 1; // Move east (increase x-coordinate)
            } elseif ($robot_state['direction'] == 'west') {
                $robot_state['position']['x'] -= 1; // Move west (decrease x-coordinate)
            }
            break;

        case 'turn left':
            // Turn the robot 90 degrees left
            if ($robot_state['direction'] == 'north') {
                $robot_state['direction'] = 'west';
            } elseif ($robot_state['direction'] == 'west') {
                $robot_state['direction'] = 'south';
            } elseif ($robot_state['direction'] == 'south') {
                $robot_state['direction'] = 'east';
            } elseif ($robot_state['direction'] == 'east') {
                $robot_state['direction'] = 'north';
            }
            break;

        case 'turn right':
            // Turn the robot 90 degrees right
            if ($robot_state['direction'] == 'north') {
                $robot_state['direction'] = 'east';
            } elseif ($robot_state['direction'] == 'east') {
                $robot_state['direction'] = 'south';
            } elseif ($robot_state['direction'] == 'south') {
                $robot_state['direction'] = 'west';
            } elseif ($robot_state['direction'] == 'west') {
                $robot_state['direction'] = 'north';
            }
            break;

        default:
            echo "Invalid command!";
    }
}

// Handling the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['move_forward'])) {
        moveRobot($robot_state, 'move forward');
    } elseif (isset($_POST['turn_left'])) {
        moveRobot($robot_state, 'turn left');
    } elseif (isset($_POST['turn_right'])) {
        moveRobot($robot_state, 'turn right');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robot Control</title>
</head>
<body>
    <h1>Robot Control Panel</h1>
    <p><strong>Current Position:</strong> (<?php echo $robot_state['position']['x']; ?>, <?php echo $robot_state['position']['y']; ?>)</p>
    <p><strong>Current Direction:</strong> <?php echo $robot_state['direction']; ?></p>

    <form method="post">
        <button type="submit" name="move_forward">Move Forward</button>
        <button type="submit" name="turn_left">Turn Left</button>
        <button type="submit" name="turn_right">Turn Right</button>
    </form>

    <h2>Updated State</h2>
    <p><strong>Position:</strong> (<?php echo $robot_state['position']['x']; ?>, <?php echo $robot_state['position']['y']; ?>)</p>
    <p><strong>Direction:</strong> <?php echo $robot_state['direction']; ?></p>
</body>
</html>
