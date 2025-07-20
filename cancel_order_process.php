<?php
session_start();
include 'db_connection.php';

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $customer_id = intval($_SESSION['customer_id']);

    // Start a transaction to ensure all deletions succeed together
    $conn->begin_transaction();

    try {
        // Check if the order belongs to the logged-in customer
        $sql_check_order = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
        $stmt_check = $conn->prepare($sql_check_order);
        $stmt_check->bind_param("ii", $order_id, $customer_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Delete from `order_details` (removes all products linked to the order)
            $sql_delete_order_details = "DELETE FROM order_details WHERE order_id = ?";
            $stmt_delete_order_details = $conn->prepare($sql_delete_order_details);
            $stmt_delete_order_details->bind_param("i", $order_id);
            $stmt_delete_order_details->execute();
            $stmt_delete_order_details->close();

            // Delete from `orders` (removes the order itself)
            $sql_delete_order = "DELETE FROM orders WHERE order_id = ?";
            $stmt_delete_order = $conn->prepare($sql_delete_order);
            $stmt_delete_order->bind_param("i", $order_id);
            $stmt_delete_order->execute();
            $stmt_delete_order->close();

            // Commit the transaction
            $conn->commit();

            $_SESSION['message'] = "Order canceled successfully.";
        } else {
            $_SESSION['message'] = "Invalid order or permission denied.";
        }

        $stmt_check->close();
    } catch (mysqli_sql_exception $exception) {
        // Roll back the transaction if something fails
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $exception->getMessage();
    }
} else {
    $_SESSION['message'] = "No order selected.";
}

$conn->close();

// Redirect back to the cancel_order.php page
header("Location: cancel_order.php");
exit();
?>
