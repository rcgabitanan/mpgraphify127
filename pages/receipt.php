<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the receipt ID from the URL
$receipt_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no valid receipt ID is provided, redirect back to admin page
if ($receipt_id <= 0) {
    header('Location: admin.php');
    exit();
}

// Fetch receipt details
$receipt_query = $conn->prepare("SELECT * FROM receipts WHERE receipt_id = ?");
$receipt_query->execute([$receipt_id]);
$receipt = $receipt_query->fetch(PDO::FETCH_ASSOC);

// If the receipt does not exist, redirect to the admin page
if (!$receipt) {
    header('Location: admin.php');
    exit();
}

// Fetch the items in the receipt
$items_query = $conn->prepare("SELECT ri.quantity, mi.item_name, mi.price 
                               FROM receipt_items ri 
                               JOIN merch_items mi ON ri.item_id = mi.item_id 
                               WHERE ri.receipt_id = ?");
$items_query->execute([$receipt_id]);
$items = $items_query->fetchAll(PDO::FETCH_ASSOC);

$total_amount = $receipt['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Details</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="receipt-page">
    <header>
        <h1 class="rhdr">Receipt Details</h1>
    </header>
    
    <button class="back-admin-button" onclick="window.location.href='admin.php'">Back to Admin Page</button>
    <main class="receipt-container">
        <h3>Receipt #<?php echo $receipt['receipt_id']; ?></h3>
        <p><strong>Total Amount: </strong>₱<?php echo number_format($total_amount, 2); ?></p>

        <h3>Items in this Receipt</h3>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
