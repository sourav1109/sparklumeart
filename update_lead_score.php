<?php
include('backend/config.php'); // Database connection

function updateLeadScore($userId, $pdo) {
    try {
        // Fetch user's current lead score
        $scoreStmt = $pdo->prepare("SELECT lead_score FROM users WHERE id = ?");
        $scoreStmt->execute([$userId]);
        $user = $scoreStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false; // User not found

        $leadScore = $user['lead_score'];

        // Check if it's the user's first purchase
        $purchaseCountStmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
        $purchaseCountStmt->execute([$userId]);
        $purchaseCount = $purchaseCountStmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

        if ($purchaseCount == 1) {
            $leadScore += 10; // First-time purchase bonus
        }

        // Check frequent buyer (more than 3 orders in last 2 months)
        $frequentBuyerStmt = $pdo->prepare("SELECT COUNT(*) as recent_orders FROM orders WHERE user_id = ? AND order_date >= DATE_SUB(NOW(), INTERVAL 2 MONTH)");
        $frequentBuyerStmt->execute([$userId]);
        $recentOrders = $frequentBuyerStmt->fetch(PDO::FETCH_ASSOC)['recent_orders'];

        if ($recentOrders > 3) {
            $leadScore += 20;
        }

        // Check high purchase value (spent > $100 in a single order)
        $highValueStmt = $pdo->prepare("SELECT MAX(total_price) as max_spent FROM orders WHERE user_id = ?");
        $highValueStmt->execute([$userId]);
        $maxSpent = $highValueStmt->fetch(PDO::FETCH_ASSOC)['max_spent'];

        if ($maxSpent > 100) {
            $leadScore += 15;
        }

        // Check regular engagement (visited site 5+ times last month)
        $engagementStmt = $pdo->prepare("SELECT COUNT(*) as visits FROM user_visits WHERE user_id = ? AND visit_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $engagementStmt->execute([$userId]);
        $visits = $engagementStmt->fetch(PDO::FETCH_ASSOC)['visits'];

        if ($visits >= 5) {
            $leadScore += 10;
        }

        // Update the lead score
        $updateStmt = $pdo->prepare("UPDATE users SET lead_score = ? WHERE id = ?");
        $updateStmt->execute([$leadScore, $userId]);

        return true;
    } catch (PDOException $e) {
        error_log("Error updating lead score: " . $e->getMessage());
        return false;
    }
}

// Example usage: Update lead score for a specific user (e.g., when they make a purchase)
if (isset($_GET['user_id'])) {
    $userId = (int)$_GET['user_id'];
    if (updateLeadScore($userId, $pdo)) {
        echo "Lead score updated successfully!";
    } else {
        echo "Failed to update lead score.";
    }
}
?>
