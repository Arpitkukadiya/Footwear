<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $payment_method = 'UPI'; // Static value for simplicity
    $payment_status = 'Completed'; // Example: assume payment is successful

    // Retrieve and sanitize UPI details
    $upi_id = filter_input(INPUT_POST, 'upi_id', FILTER_SANITIZE_STRING);

    // Validate input fields
    if (empty($order_id) || empty($total_amount) || empty($upi_id)) {
        echo "All fields are required.";
        exit();
    }

    if (!is_numeric($total_amount) || $total_amount <= 0) {
        echo "Invalid total amount.";
        exit();
    }

    // Adjust regex for UPI ID validation
    // A more permissive regex that matches UPI IDs
    if (!preg_match('/^[a-zA-Z0-9@._-]+$/', $upi_id)) {
        echo "Invalid UPI ID format.";
        exit();
    }

    // Prepare and execute SQL for inserting payment record
    $sql = "INSERT INTO payments (customer_id, order_id, payment_method, payment_status, total_amount, payment_date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $_SESSION['customer_id'], $order_id, $payment_method, $payment_status, $total_amount);

    if ($stmt->execute()) {
        echo "Payment processed successfully.";
    } else {
        echo "Error processing payment: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request method.";
}

// Close database connection
$conn->close();
?>
