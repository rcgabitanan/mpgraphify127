<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new receipt
    $total_amount = 0;
    $stmt = $conn->prepare("INSERT INTO receipts (total_amount) VALUES (?)");
    $stmt->execute([0]); // Temporary 0 total amount

    // Get the receipt ID of the newly created receipt
    $receipt_id = $conn->lastInsertId();

    // Process the selected items and their quantities
    foreach ($_POST['item_id'] as $index => $item_id) {
        $quantity = $_POST['quantity'][$index];

        if ($quantity > 0) {
            // Get item price from the database
            $item_stmt = $conn->prepare("SELECT price FROM merch_items WHERE item_id = ?");
            $item_stmt->execute([$item_id]);
            $item = $item_stmt->fetch(PDO::FETCH_ASSOC);
            $item_price = $item['price'];

            // Calculate total price for this item
            $total_item_price = $item_price * $quantity;

            // Insert into receipt_items table (include item_price)
            $stmt = $conn->prepare("INSERT INTO receipt_items (receipt_id, item_id, quantity, item_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$receipt_id, $item_id, $quantity, $item_price]);

            // Update total amount for the receipt
            $total_amount += $total_item_price;
        }
    }

    // Update the total amount of the receipt
    $update_stmt = $conn->prepare("UPDATE receipts SET total_amount = ? WHERE receipt_id = ?");
    $update_stmt->execute([$total_amount, $receipt_id]);

    // Redirect to the admin page or receipt page after creation
    header("Location: receipt.php?id=" . $receipt_id);
    exit();
}

// Fetch all merch items for the selection
$items_query = $conn->query("SELECT * FROM merch_items");
$items = $items_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Receipt</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="create-receipt-page">
    <button class="back-admin-button" onclick="window.location.href='admin.php'">Back to Admin Page</button>
    <div class="cr-container">
        <form action="create_receipt.php" method="POST">
            <h3 class="hdr">Select Items to Add to Receipt</h3>

            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="checkbox" name="item_id[]" value="<?php echo $item['item_id']; ?>" class="item-checkbox">
                                <input type="number" name="quantity[]" placeholder="Quantity" min="1" class="quantity-input" disabled>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button class="create-receipt-button" type="submit">Create Receipt</button>
        </form>
    </div> 

    <script>
        // Enable quantity input when checkbox is checked
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function() {
                const quantityInput = document.querySelectorAll('.quantity-input')[index];
                quantityInput.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    quantityInput.value = ''; // Clear quantity if unchecked
                }
            });
        });
    </script>
</body>
</html>
