<?php
require_once '../includes/db.php';

// Query to fetch total merchandise items sold
$total_items_sold_query = "SELECT SUM(quantity) AS total_sold FROM receipt_items";
$total_items_sold_result = $conn->query($total_items_sold_query);
$total_items_sold = $total_items_sold_result->fetch(PDO::FETCH_ASSOC)['total_sold'] ?? 0;

// Query to fetch the best-selling merchandise item
$best_selling_item_query = "
    SELECT m.item_name, SUM(ri.quantity) AS total_quantity
    FROM receipt_items ri
    JOIN merch_items m ON ri.item_id = m.item_id
    GROUP BY m.item_name
    ORDER BY total_quantity DESC
    LIMIT 1";
$best_selling_item_result = $conn->query($best_selling_item_query);
$best_selling_item = $best_selling_item_result->fetch(PDO::FETCH_ASSOC);
$best_selling_item_name = $best_selling_item['item_name'] ?? 'None';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>

    <link rel="stylesheet" type="text/css" href="../index.css">
</head>

<body class="index-page">
    <section>
        <button class="login" onclick="window.location.href='login.php'">Login</button>
    </section>

    <section>
        <h2 class="total-sold"><?php echo $total_items_sold; ?></h2>
    </section>

    <section>
        <button class="image-button150" onclick="window.location.href='merch_items_basic_user.php'">
            <img src="../clear button 150.png" alt="Button Image">
        </button>
    </section>

    <section>
        <h2 class="best-selling"><?php echo $best_selling_item_name; ?></h2>
    </section>
</body>
</html>
