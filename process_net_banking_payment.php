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
    $payment_method = 'Net Banking';
    $payment_status = 'Completed';

    // Retrieve and sanitize net banking details
    $bank_account = filter_input(INPUT_POST, 'bank_account', FILTER_SANITIZE_STRING);
    $ifsc_code = filter_input(INPUT_POST, 'ifsc_code', FILTER_SANITIZE_STRING);
    $security_pin = filter_input(INPUT_POST, 'security_pin', FILTER_SANITIZE_STRING);

    // Validate input fields
    if (empty($order_id) || empty($total_amount) || empty($bank_account) || empty($ifsc_code) || empty($security_pin)) {
        echo "All fields are required.";
        exit();
    }

    if (!is_numeric($total_amount) || $total_amount <= 0) {
        echo "Invalid total amount.";
        exit();
    }

    if (!preg_match('/^\d{12,16}$/', $bank_account)) {
        echo "Invalid bank account number.";
        exit();
    }

    if (!preg_match('/^[A-Z]{4}\d{7}$/', $ifsc_code)) {
        echo "Invalid IFSC code.";
        exit();
    }

    if (!preg_match('/^\d{4,6}$/', $security_pin)) {
        echo "Invalid security PIN.";
        exit();
    }

    // Simulate payment processing (this is where actual payment integration would occur)
    sleep(2);  // Simulate processing time

    // Prepare and execute SQL for inserting payment record
    $sql = "INSERT INTO payments (customer_id, order_id, payment_method, payment_status, total_amount, payment_date) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "SQL prepare error: " . $conn->error;
        exit();
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("iissd", $_SESSION['customer_id'], $order_id, $payment_method, $payment_status, $total_amount);

    // Execute the statement
    if ($stmt->execute()) {
        // Display success message
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
