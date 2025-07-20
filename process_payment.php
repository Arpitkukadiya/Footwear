<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $order_id = $_POST['order_id'];  // Order ID from form
    $payment_method = $_POST['payment_method'];  // Payment method from form
    $payment_status = 'Completed';  // Example: assume payment is successful
    $total_amount = $_POST['total_amount'];  // Total amount from form

    // Insert payment record into the database
    $sql = "INSERT INTO payments (customer_id, order_id, payment_method, payment_status, amount, payment_date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $_SESSION['customer_id'], $order_id, $payment_method, $payment_status, $total_amount);
    
    if ($stmt->execute()) {
        echo "Payment processed successfully.";
    } else {
        echo "Error processing payment.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
