<?php

include('../config.php');

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user painting requests
$requestStmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ?");
$requestStmt->execute([$userId]);
$paintingRequests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0044cc;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
        }
        .header .nav-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            font-weight: 500;
        }
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: calc(33.333% - 20px);
            padding: 15px;
            box-sizing: border-box;
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
        }
        .dashboard-card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .details p {
            margin: 5px 0;
            color: #555;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        button[name="response"][value="Accepted"] {
            background-color: #28a745;
        }
        button[name="response"][value="Declined"] {
            background-color: #dc3545;
        }
        .response-confirmed {
            font-weight: bold;
            color: #28a745;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Display confirmation or error messages for payment -->
    <?php if (isset($_SESSION['payment_confirmation'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['payment_confirmation']; ?>
        </div>
        <?php unset($_SESSION['payment_confirmation']); ?>
    <?php elseif (isset($_SESSION['payment_error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['payment_error']; ?>
        </div>
        <?php unset($_SESSION['payment_error']); ?>
    <?php endif; ?>

    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="nav-links">
            <a href="../../upload.php">Upload New Request</a>
            <a href="view_orders.php">View All Orders</a>
            <a href="../logout.php">Logout</a>
            <a href="../../sell.php">Art Shop</a>
        </div>
    </div>
    
    <h2>Your Painting Requests</h2>
    <div class="dashboard">
        <?php if ($paintingRequests): ?>
            <?php foreach ($paintingRequests as $request): ?>
                <div class="dashboard-card">
                    <img src="../../<?php echo htmlspecialchars($request['image_path']); ?>" alt="Request Image">
                    <div class="details">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($request['name']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($request['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($request['phone']); ?></p>
                        <p><strong>Page Size:</strong> <?php echo htmlspecialchars($request['page_size']); ?></p>
                        <p><strong>Background Type:</strong> <?php echo htmlspecialchars($request['background_type']); ?></p>
                        <p><strong>Paper Type:</strong> <?php echo htmlspecialchars($request['paper_type']); ?></p>
                        <p><strong>Upload Date:</strong> <?php echo htmlspecialchars($request['upload_date']); ?></p>
                        <p><strong>Proposed Amount:</strong> $<?php echo htmlspecialchars($request['proposed_amount'] ?? 'Pending'); ?></p>

                        <!-- Display tentative date based on status -->
                        <?php if ($request['status'] === 'Approved'): ?>
                            <p><strong>Tentative Date:</strong> 
                                <?php echo !empty($request['tentative_date']) ? htmlspecialchars($request['tentative_date']) : 'Yet to come'; ?>
                            </p>
                            
                            <!-- Display bill images if tentative_date is set -->
                            <?php if (!empty($request['tentative_date']) && !empty($request['bill_image'])): ?>
                                <div class="bill-images">
                                    <strong>Bill Images:</strong>
                                    <?php 
                                    // Assume bill_image contains comma-separated paths for multiple images
                                    $billImages = explode(',', $request['bill_image']);
                                    foreach ($billImages as $imagePath): ?>
                                        <img src="<?php echo htmlspecialchars(trim($imagePath)); ?>" alt="Bill Image" style="max-width: 100%; border-radius: 8px; margin: 10px 0;">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Display response buttons only if price_updated is 1, status is NULL, and user has not responded -->
                        <?php if ($request['price_updated'] == 1 && $request['status'] !== 'Approved'&&$request['status'] !== 'Declined' ): ?>
                            <form action="handle_user_response.php" method="POST" class="button-group">
                                <input type="hidden" name="upload_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="response" value="Accepted">Yes</button>
                                <button type="submit" name="response" value="Declined">No</button>
                            </form>
                        <?php elseif (!empty($request['user_response'])): ?>
                            <p class="response-confirmed">Response: <?php echo htmlspecialchars($request['user_response']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No painting requests found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
