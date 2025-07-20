<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if (isset($_POST['order_id']) && isset($_POST['bank_name']) && isset($_POST['account_number'])) {
    $order_id = intval($_POST['order_id']);
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);

    // Simulate payment processing
    $sql = "UPDATE orders SET payment_status = 'Paid' WHERE order_id = ? AND payment_status = 'Processing Payment'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Insert into payments table
        $sql = "INSERT INTO payments (order_id, customer_id, payment_method, amount, payment_status, payment_date) VALUES (?, ?, 'net_banking', (SELECT total_amount FROM orders WHERE order_id = ?), 'Paid', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $order_id, $_SESSION['customer_id'], $order_id);
        $stmt->execute();

        // Display success message instead of redirecting
        echo "Payment successful. Your order has been processed.";
    } else {
        echo "Failed to complete payment. Please try again.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
