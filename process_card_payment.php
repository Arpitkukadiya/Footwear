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
    $payment_method = 'Card'; // Static value for simplicity
    $payment_status = 'Completed'; // Example: assume payment is successful

    // Retrieve and sanitize card details
    $card_number = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
    $expiry_date = filter_input(INPUT_POST, 'expiry_date', FILTER_SANITIZE_STRING);
    $cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING);

    // Validate input fields
    if (empty($order_id) || empty($total_amount) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
        echo "All fields are required.";
        exit();
    }

    if (!is_numeric($total_amount) || $total_amount <= 0) {
        echo "Invalid total amount.";
        exit();
    }

    if (!preg_match('/^\d{13,19}$/', $card_number)) {
        echo "Invalid card number.";
        exit();
    }

    if (!preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
        echo "Invalid expiry date format.";
        exit();
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        echo "Invalid CVV.";
        exit();
    }

    // Prepare and execute SQL for inserting payment record
    $sql = "INSERT INTO payments (customer_id, order_id, payment_method, payment_status, total_amount, payment_date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $_SESSION['customer_id'], $order_id, $payment_method, $payment_status, $total_amount);

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
