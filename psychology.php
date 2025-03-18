<?php
// Database Connection
$servername = "localhost";
$username = "root";  // Update if needed
$password = "";      // Set your password if any
$dbname = "art_therapy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize response variables
$finalMessage = "";
$finalPoem = "";
$submissionSuccess = false;
$submissionError = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $mood = trim($_POST['mood']);
    $painting = trim($_POST['painting']);

    if (!empty($name) && !empty($mood) && !empty($painting)) {
        // Define Messages & Poems based on mood
        $messages = [
            "happy" => "Your energy shines like the sun, bringing warmth to all!",
            "calm" => "Your peaceful aura is like a gentle wave, soothing all around you.",
            "energetic" => "You are a spark of excitement, always ready for adventure!",
            "anxious" => "Even in the storm, you find strength within.",
            "sad" => "Like the rain, your emotions nourish and bring new beginnings."
        ];

        $poems = [
            "happy" => "Oh $name, your joy is bright,\nLike the stars in the endless night.",
            "calm" => "$name, you flow like a peaceful stream,\nCarrying dreams with a silent gleam.",
            "energetic" => "$name, a lightning strike so free,\nDancing wild like the roaring sea.",
            "anxious" => "$name, though storms may rise and fall,\nYour strength will always stand tall.",
            "sad" => "$name, your heart’s a moonlit shore,\nQuietly shining forevermore."
        ];

        $finalMessage = $messages[$mood] ?? "You are unique and full of wonder!";
        $finalPoem = $poems[$mood] ?? "$name, your spirit is rare,\nA beauty beyond all compare.";

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO art_therapy_users (name, mood, painting, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $mood, $painting);

        if ($stmt->execute()) {
            $submissionSuccess = true;
        } else {
            $submissionError = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $submissionError = "Please complete all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art & Soul Quest</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            font-family: 'Poppins', sans-serif;
            text-align: center;
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin: auto;
        }
        .step {
            display: none;
        }
        .active {
            display: block;
        }
        .button {
            background: #ffcc00;
            color: black;
            padding: 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background 0.3s;
        }
        .button:hover {
            background: #e6b800;
        }
        .options div {
            padding: 15px;
            margin: 10px;
            border-radius: 10px;
            background: #555;
            cursor: pointer;
            transition: 0.3s;
        }
        .options div:hover {
            background: #777;
        }
        .painting-options img {
            width: 120px;
            height: 120px;
            margin: 10px;
            cursor: pointer;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Art & Soul Quest 🌌</h1>

        <?php if ($submissionSuccess): ?>
            <h2>🔮 Your Personalized Art Message</h2>
            <p><?php echo $finalMessage; ?></p>
            <h3>🌟 Your Personalized Poem</h3>
            <p><?php echo nl2br($finalPoem); ?></p>
        <?php elseif ($submissionError): ?>
            <p>Error: <?php echo $submissionError; ?></p>
        <?php else: ?>
            <div id="step1" class="step active">
                <h2>🌟 Choose the World That Matches Your Energy</h2>
                <div class="options">
                    <div onclick="nextStep('happy')">🏰 Joyful Kingdom</div>
                    <div onclick="nextStep('calm')">🌊 Peaceful Beach</div>
                    <div onclick="nextStep('energetic')">⚡ Neon City</div>
                    <div onclick="nextStep('anxious')">🌪 Mysterious Forest</div>
                    <div onclick="nextStep('sad')">🌧 Rainy Evening</div>
                </div>
            </div>

            <div id="step2" class="step">
                <h2>🎨 Choose Your Favorite Painting</h2>
                <div class="painting-options">
                    <img src="../../4.jpg" alt="Abstract" onclick="nextStep('abstract')">
                    <img src="../../background7.jpg" alt="Realistic" onclick="nextStep('realistic')">
                    <img src="../../f.jpg" alt="Minimalist" onclick="nextStep('minimalist')">
                    <img src="../../ar.jpg" alt="Surreal" onclick="nextStep('surreal')">
                </div>
            </div>

            <div id="step3" class="step">
                <h2>🔮 Reveal Your Art Destiny!</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" id="mood" name="mood">
                    <input type="hidden" id="painting" name="painting">
                    <label for="name">Enter Your Name:</label>
                    <input type="text" id="name" name="name" required>
                    <button type="submit" class="button">See Your Personalized Art</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function nextStep(value) {
            let current = document.querySelector('.step.active');
            if (current.id === 'step1') document.getElementById('mood').value = value;
            if (current.id === 'step2') document.getElementById('painting').value = value;
            current.classList.remove('active');
            current.nextElementSibling.classList.add('active');
        }
    </script>
</body>
</html>