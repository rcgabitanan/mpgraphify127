<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all receipts
$stmt = $conn->query("SELECT * FROM receipts");
$receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total orders and total sales
$order_count = count($receipts);
$total_sales = 0;
foreach ($receipts as $receipt) {
    $total_sales += $receipt['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="admin-page">
    <main>
        <p class="total-orders"><?php echo $order_count; ?></p>
        <p class="total-sales"><?php echo number_format($total_sales, 2); ?></p>

        <table class="receipt-list">
            <thead>
                <tr>
                    <th>Receipt ID</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipts as $receipt): ?>
                    <tr>
                        <td><?php echo $receipt['receipt_id']; ?></td>
                        <td>₱<?php echo number_format($receipt['total_amount'], 2); ?></td>
                        <td>
                            <a href="receipt_details.php?id=<?php echo $receipt['receipt_id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button class="create-receipt" onclick="window.location.href='create_receipt.php'">
            <img src="../clear button.png" alt="Button Image">
        </button>
        <button class="manage-merch" onclick="window.location.href='merch_items_admin.php'">
            <img src="../clear button 150.png" alt="Button Image">
        </button>
        <button class="login" onclick="window.location.href='logout.php'">Logout</button>
    </main>
</body>
</html>
