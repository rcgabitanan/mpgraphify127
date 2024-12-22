<?php
include './includes/db.php';

try {
    // Run a simple query to test connection
    $stmt = $conn->query("SELECT DATABASE()");
    $databaseName = $stmt->fetchColumn();

    echo "Connected successfully to database: $databaseName";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
