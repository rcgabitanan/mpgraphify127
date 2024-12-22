<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if item_id is passed
if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    header('Location: merch_items_admin.php');
    exit();
}

$item_id = $_GET['item_id'];

// Fetch current item details
$query = $conn->prepare("SELECT * FROM merch_items WHERE item_id = ?");
$query->execute([$item_id]);
$item = $query->fetch(PDO::FETCH_ASSOC);

// Redirect if item not found
if (!$item) {
    header('Location: merch_items_admin.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    $errors = [];

    // Validate input
    if (empty($item_name)) {
        $errors[] = "Item name is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errors[] = "Price must be a positive number.";
    }
    if (empty($stock_quantity) || !is_numeric($stock_quantity) || $stock_quantity < 0) {
        $errors[] = "Stock quantity must be a non-negative number.";
    }

    // If no errors, update the item in the database
    if (empty($errors)) {
        try {
            $update_query = $conn->prepare("UPDATE merch_items SET item_name = ?, description = ?, price = ?, stock_quantity = ? WHERE item_id = ?");
            $update_query->execute([$item_name, $description, $price, $stock_quantity, $item_id]);

            // Redirect to merch items admin page after successful update
            header('Location: merch_items_admin.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Merchandise Item</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="merch-item-edit-page">
    <button class="back-merch-admin-button" onclick="window.location.href='merch_items_admin.php'">Back to Merch Items Admin</button>
    <main class="merch-edit-container">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label class="labels" for="item_name">Item Name</label><br>
            <input type="text" id="item_name" class="field" name="item_name" required value="<?php echo htmlspecialchars($item['item_name']); ?>"><br>

            <label class="labels" for="description">Description</label><br>
            <textarea id="description" class="field" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea><br>

            <label class="labels" for="price">Price (₱)</label><br>
            <input type="number" id="price" class="field" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars($item['price']); ?>"><br>

            <label class="labels" for="stock_quantity">Stock Quantity</label><br>
            <input type="number" id="stock_quantity" class="field" name="stock_quantity" min="0" required value="<?php echo htmlspecialchars($item['stock_quantity']); ?>"><br>

            <button class="login-button" type="submit">Update Item</button>
        </form>
    </main>
</body>
</html>
