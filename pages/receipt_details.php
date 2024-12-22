<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get receipt ID from the URL
if (isset($_GET['id'])) {
    $receipt_id = $_GET['id'];

    // Fetch receipt details
    $stmt = $conn->prepare("SELECT * FROM receipts WHERE receipt_id = ?");
    $stmt->execute([$receipt_id]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        echo "Receipt not found.";
        exit();
    }

    // Fetch receipt items
    $items_stmt = $conn->prepare("SELECT ri.*, mi.item_name, mi.price FROM receipt_items ri
                                  JOIN merch_items mi ON ri.item_id = mi.item_id
                                  WHERE ri.receipt_id = ?");
    $items_stmt->execute([$receipt_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "No receipt ID provided.";
    exit();
}

// Delete receipt functionality
if (isset($_POST['delete_receipt'])) {
    // Delete receipt items first
    $delete_items_stmt = $conn->prepare("DELETE FROM receipt_items WHERE receipt_id = ?");
    $delete_items_stmt->execute([$receipt_id]);

    // Delete the receipt
    $delete_receipt_stmt = $conn->prepare("DELETE FROM receipts WHERE receipt_id = ?");
    $delete_receipt_stmt->execute([$receipt_id]);

    // Redirect to admin page after deletion
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Details</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="receipt-details-page">
    <header>
        <h1 class="rhdr">Receipt Details</h1>
    </header>
    <button class="back-admin-button" onclick="window.location.href='admin.php'">Back to Admin Page</button>
    <main class="receipt-container">
        <h2>Receipt #<?php echo $receipt['receipt_id']; ?></h2>
        <p>Total Amount: ₱<?php echo number_format($receipt['total_amount'], 2); ?></p>

        <h3>Items in this Receipt:</h3>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td>₱<?php echo number_format($item['item_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₱<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form action="receipt_details.php?id=<?php echo $receipt['receipt_id']; ?>" method="POST">
            <button class="delete-button" type="submit" name="delete_receipt" onclick="return confirm('Are you sure you want to delete this receipt?')">Delete Receipt</button>
        </form>
    </main>
</body>
</html>
