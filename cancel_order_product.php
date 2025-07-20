<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if (isset($_POST['order_detail_id'])) {
    $order_detail_id = intval($_POST['order_detail_id']);
    $customer_id = intval($_SESSION['customer_id']);

    // Start a transaction to ensure the deletion succeeds
    $conn->begin_transaction();

    try {
        // Delete the specific product from the order_details table
        $sql_delete_order_detail = "DELETE FROM order_details WHERE order_detail_id = ? AND order_id IN (SELECT order_id FROM orders WHERE customer_id = ?)";
        $stmt_delete = $conn->prepare($sql_delete_order_detail);
        $stmt_delete->bind_param("ii", $order_detail_id, $customer_id);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            $_SESSION['message'] = "Product canceled successfully.";
        } else {
            $_SESSION['message'] = "Error: Unable to cancel the product. It may not belong to you.";
        }

        // Commit the transaction
        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        // Roll back the transaction if something fails
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $exception->getMessage();
    }
} else {
    $_SESSION['message'] = "No product selected.";
}

$conn->close();

// Redirect back to the orders page
header("Location: order.php");
exit();
?>
