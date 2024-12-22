<?php
session_start();
require_once '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

    // If no errors, insert new item into database
    if (empty($errors)) {
        try {
            $insert_query = $conn->prepare("INSERT INTO merch_items (item_name, description, price, stock_quantity) VALUES (?, ?, ?, ?)");
            $insert_query->execute([$item_name, $description, $price, $stock_quantity]);

            // Redirect to merch items admin page after successful insertion
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
    <title>Add Merchandise Item</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body class="add-merch-page">
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
            <input type="text" id="item_name" class="field" name="item_name" required value="<?php echo htmlspecialchars($item_name ?? ''); ?>"><br>

            <label class="labels" for="description">Description</label><br>
            <textarea id="description" class="field" name="description" required><?php echo htmlspecialchars($description ?? ''); ?></textarea><br>

            <label class="labels" for="price">Price (₱)</label><br>
            <input type="number" id="price" class="field" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars($price ?? ''); ?>"><br>

            <label class="labels" for="stock_quantity">Stock Quantity</label><br>
            <input type="number" id="stock_quantity" class="field" name="stock_quantity" min="0" required value="<?php echo htmlspecialchars($stock_quantity ?? ''); ?>"><br>

            <button class="login-button" type="submit">Add Item</button>
        </form>
    </main>
</body>
</html>
