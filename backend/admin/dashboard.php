<?php
session_start();
include('../config.php');

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all uploads with order details
$stmt = $pdo->query("
    SELECT uploads.*, uploads.proposed_amount, uploads.status, 
           uploads.tentative_date, uploads.bill_image
    FROM uploads
");
$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - User Uploads</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        /* Additional styling for the admin dashboard */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #333;
            color: #fff;
        }
        header h1 {
            margin: 0;
        }
        header a {
            color: #ffdddd;
            text-decoration: none;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 1rem;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        img {
            border-radius: 4px;
        }
        .no-data {
            text-align: center;
            color: #666;
            margin: 2rem 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard - User Upload Submissions</h1>
        <a href="manage_sell_page.php">Manage Sell Page</a>
        <a href="manage_gallery.php" class="btn-admin-nav">Manage Gallery</a>
        <a href="manage_leads.php" class="btn-admin-nav">📊 Manage Leads</a>

        <a href="manage_orders.php" class="btn-admin-nav">Manage orders</a>
        <a href="../logout.php">Logout</a>

    </header>

    <div class="container">
        <h2>User Upload Submissions</h2>

        <?php if (empty($uploads)): ?>
            <p class="no-data">No user submissions found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Image</th>
                        <th>Proposed Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uploads as $upload): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($upload['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($upload['name']); ?></td>
                        <td><?php echo htmlspecialchars($upload['email']); ?></td>
                        <td><?php echo htmlspecialchars($upload['phone']); ?></td>
                        <td>
                            <?php if (!empty($upload['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($upload['image_path']); ?>" alt="Uploaded Image" width="100">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($upload['status'] === 'Pending Approval' || empty($upload['proposed_amount'])): ?>
                                <form action="set_proposed_amount.php" method="POST">
                                    <input type="hidden" name="upload_id" value="<?php echo htmlspecialchars($upload['id']); ?>">
                                    <input type="number" name="proposed_amount" placeholder="Amount" required>
                                    <button type="submit">Set Amount</button>
                                </form>
                            <?php else: ?>
                                $<?php echo htmlspecialchars($upload['proposed_amount']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($upload['status'] ?? ''); ?></td>
                        <td>
                            <?php if (($upload['status'] === 'Approved' || $upload['status'] === 'Paid') && empty($upload['tentative_date'])): ?>
                                <!-- Form for setting tentative date if status is Approved or Paid and tentative_date is null -->
                                <form action="set_tentative_date.php" method="POST">
                                    <input type="hidden" name="upload_id" value="<?php echo htmlspecialchars($upload['id']); ?>">
                                    <input type="date" name="tentative_date" required>
                                    <button type="submit">Set Tentative Date</button>
                                </form>
                                <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php elseif (isset($_GET['message']) && $_GET['message'] == 'DateSet'): ?>
    <div class="alert alert-success">
        Tentative date has been set successfully.
    </div>
<?php endif; ?>

                            <?php elseif ($upload['status'] === 'Dispatched' && empty($upload['bill_image'])): ?>
                                <!-- Form for uploading bill if status is Dispatched and bill_image is not set -->
                                <form action="dispatch_order.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="upload_id" value="<?php echo htmlspecialchars($upload['id']); ?>">
                                    <input type="file" name="bill_image" accept="image/*" required>
                                    <button type="submit">Upload Bill</button>
                                </form>
                            <?php elseif ($upload['status'] === 'Delivered'): ?>
                                <span>Delivery Confirmed</span>
                            <?php elseif (!empty($upload['tentative_date'])): ?>
                                <span><?php echo htmlspecialchars($upload['tentative_date']); ?></span>
                            <?php else: ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
    <?php if ($upload['status'] === 'Approved' && !empty($upload['tentative_date']) && empty($upload['bill_image'])): ?>
        <!-- Form for uploading bill image if status is Approved, tentative date is set, and bill image is not yet uploaded -->
        <form action="upload_bill.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload_id" value="<?php echo htmlspecialchars($upload['id']); ?>">
            <input type="file" name="bill_image" accept="image/*" required>
            <button type="submit">Upload Bill</button>
        </form>
    <?php elseif (!empty($upload['bill_image'])): ?>
        <!-- Show bill image thumbnail and set up full-screen preview -->
        <img src="../<?php echo htmlspecialchars($upload['bill_image']); ?>" alt="Bill Image" width="100" style="cursor:pointer;" onclick="showFullScreenImage('../<?php echo htmlspecialchars($upload['bill_image']); ?>')">
    <?php else: ?>
        <span>N/A</span>
    <?php endif; ?>
</td>


                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Full-screen image preview overlay -->
<div id="imageOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); z-index:1000;">
    <span style="position:absolute; top:20px; right:20px; font-size:30px; color:white; cursor:pointer;" onclick="closeFullScreenImage()">✖</span>
    <img id="fullScreenImage" src="" style="display:block; margin:auto; max-width:90%; max-height:90%; position:absolute; top:0; bottom:0; left:0; right:0;">
</div>

<script>
    function showFullScreenImage(imageSrc) {
        document.getElementById("fullScreenImage").src = imageSrc;
        document.getElementById("imageOverlay").style.display = "block";
    }

    function closeFullScreenImage() {
        document.getElementById("imageOverlay").style.display = "none";
    }
</script>

        <?php endif; ?>
    </div>
</body>
</html>
