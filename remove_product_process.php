<?php
session_start();
include 'db_connection.php';

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Start a transaction to ensure both deletions succeed
    $conn->begin_transaction();

    try {
        // Delete associated records from the orders table (if applicable)
        $sql_delete_order = "DELETE FROM orders WHERE product_id = ?";
        $stmt_order = $conn->prepare($sql_delete_order);
        $stmt_order->bind_param("i", $product_id);
        $stmt_order->execute();
        $stmt_order->close();

        // Delete the product from the products table
        $sql_delete_product = "DELETE FROM products WHERE product_id = ?";
        $stmt_product = $conn->prepare($sql_delete_product);
        $stmt_product->bind_param("i", $product_id);
        $stmt_product->execute();
        $stmt_product->close();

        // Commit the transaction
        $conn->commit();

        $_SESSION['message'] = "Product removed successfully along with associated orders.";
    } catch (mysqli_sql_exception $exception) {
        // Rollback the transaction if something fails
        $conn->rollback();

        $_SESSION['message'] = "Error: " . $exception->getMessage();
    }
} else {
    $_SESSION['message'] = "No product selected.";
}

// Close the database connection
$conn->close();

// Redirect to the remove products page
header("Location: remove_product.php");
exit();
?>
