<?php
// Start session
session_start();

// Include database configuration
include(__DIR__ . '/../config.php'); // Ensures the correct path to config.php

// Check if admin is logged in
checkUserRole('admin'); // Redirects to login if not an admin

// Get sorting option
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'lead_score';

// Validate sorting option
$allowed_sorts = ['lead_score', 'last_activity', 'created_at'];
if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'lead_score';
}

// Fetch users sorted by the selected column
try {
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY $sort_by DESC");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leads</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add CSS for styling -->
</head>
<body>
    <h2>Manage Leads</h2>
    <a href="admin_dashboard.php">Back to Dashboard</a>

    <form method="GET">
        <label for="sort_by">Sort by:</label>
        <select name="sort_by" id="sort_by" onchange="this.form.submit()">
            <option value="lead_score" <?= $sort_by == 'lead_score' ? 'selected' : '' ?>>Lead Score</option>
            <option value="last_activity" <?= $sort_by == 'last_activity' ? 'selected' : '' ?>>Last Activity</option>
            <option value="created_at" <?= $sort_by == 'created_at' ? 'selected' : '' ?>>Signup Date</option>
        </select>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Lead Score</th>
                <th>Last Activity</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= $user['lead_score'] ?></td>
                    <td><?= $user['last_activity'] ?></td>
                    <td><?= $user['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
