<?php
session_start();
require_once '../includes/db.php';

// Fetch all merchandise items
$merch_query = "SELECT * FROM merch_items";
$merch_result = $conn->query($merch_query);
$merch_items = $merch_result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Merchandise Items</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="merch-items-basic-user-page">
    <button class="back-admin-button" onclick="window.location.href='index.php'">Back to Home</button>
    <main class="merch-container-basic-user">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merch_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['stock_quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
