<?php
session_start();
include 'db_connection.php';

// Check if order_id and product_id are set and user is logged in
if (isset($_POST['order_id'], $_POST['product_id']) && isset($_SESSION['customer_id'])) {
    $order_id = intval($_POST['order_id']);
    $product_id = intval($_POST['product_id']);
    $customer_id = intval($_SESSION['customer_id']);

    // Start a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Check if the order belongs to the logged-in customer
        $sql_check_order = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ? AND product_id = ?";
        $stmt_check = $conn->prepare($sql_check_order);
        $stmt_check->bind_param("iii", $order_id, $customer_id, $product_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Delete the specific product from the order
            $sql_delete_product = "DELETE FROM orders WHERE order_id = ? AND product_id = ?";
            $stmt_delete_product = $conn->prepare($sql_delete_product);
            $stmt_delete_product->bind_param("ii", $order_id, $product_id);
            $stmt_delete_product->execute();
            $stmt_delete_product->close();

            // Commit the transaction
            $conn->commit();

            $_SESSION['message'] = "Product canceled successfully.";
        } else {
            $_SESSION['message'] = "Invalid order or permission denied.";
        }

        $stmt_check->close();
    } catch (mysqli_sql_exception $exception) {
        // Rollback the transaction if something fails
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $exception->getMessage();
    }
} else {
    $_SESSION['message'] = "No product selected.";
}

$conn->close();

// Redirect back to the order page
header("Location: order.php");
exit();
