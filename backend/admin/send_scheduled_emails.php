<?php
// Include database connection
include_once __DIR__ . "/../config.php";

// Check if the email script has already run today
$today = date('Y-m-d');
$checkQuery = "SELECT COUNT(*) FROM email_logs WHERE date_sent = ?";
$stmt = $pdo->prepare($checkQuery);
$stmt->execute([$today]);
$alreadySent = $stmt->fetchColumn();

if ($alreadySent > 0) {
    return; // Exit if emails have already been sent today
}

// Fetch users who need emails
$query = "SELECT id, email, lead_score, last_email_sent 
          FROM users 
          WHERE (DATEDIFF(?, last_email_sent) >= 7 AND lead_score >= 100) 
             OR (DATEDIFF(?, last_email_sent) >= 0.01 AND lead_score < 100)";
$stmt = $pdo->prepare($query);
$stmt->execute([$today, $today]);
$users = $stmt->fetchAll();

foreach ($users as $user) {
    $userId = $user['id'];
    $email = $user['email'];
    $leadScore = $user['lead_score'];

    // Email content
    if ($leadScore >= 100) {
        $subject = "Exclusive Offer for Our Valued Customers!";
        $message = "<p>Dear customer,</p><p>As a valued user, you get an exclusive discount on our latest artworks!</p><p>Check it out now!</p>";
    } else {
        $subject = "We Miss You! Special Offer Inside";
        $message = "<p>Hey there,</p><p>We noticed you haven’t been active lately! Here’s a special deal to bring you back!</p>";
    }

    // Send email function
    $headers = "From: no-reply@sparklumeart.com\r\n";
    $headers .= "Reply-To: sourav092002@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($email, $subject, $message, $headers)) {
        // Update last_email_sent date
        $updateQuery = "UPDATE users SET last_email_sent = ? WHERE id = ?";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$today, $userId]);

        // Log the email sent
        $logQuery = "INSERT INTO email_logs (user_id, email, date_sent) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($logQuery);
        $stmt->execute([$userId, $email, $today]);
    }
}
?>
