<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all merchandise items
$merch_query = "SELECT * FROM merch_items";
$merch_result = $conn->query($merch_query);
$merch_items = $merch_result->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $delete_item_id = $_POST['delete_item_id'];

    // Delete item from database
    $delete_query = $conn->prepare("DELETE FROM merch_items WHERE item_id = ?");
    $delete_query->execute([$delete_item_id]);

    // Refresh the page to reflect changes
    header("Location: merch_items_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Merchandise Items</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="merch-items-admin-page">
    <button class="back-admin-button" onclick="window.location.href='admin.php'">Back to Admin Page</button>
    <main class="merch-container">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merch_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['stock_quantity']; ?></td>
                        <td>
                            <a href="merch_item_edit.php?item_id=<?php echo $item['item_id']; ?>">Edit</a>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="delete_item_id" value="<?php echo $item['item_id']; ?>">
                                <button class="deleter" type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button class="add-merch-button" onclick="window.location.href='add_merch_item.php'">Add New Item</button>
    </main>
</body>
</html>
