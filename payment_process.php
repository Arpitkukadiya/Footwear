<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id']) || !isset($_POST['order_id'])) {
    echo "Invalid request.";
    exit();
}

$order_id = $_POST['order_id'];
$customer_id = $_SESSION['customer_id'];
$total_amount = $_POST['total_amount'];

// Get the payment method from POST data
$payment_method = $_POST['payment_method'];

// Simulate payment processing (for demo purposes)
// In real-world scenarios, integrate with a payment gateway

// Example: payment result
$payment_success = true; // This should be based on actual payment response
$payment_status = $payment_success ? "Paid" : "Failed";

// Insert payment details into the payments table
$sql = "INSERT INTO payments (customer_id, order_id, payment_method, payment_status, amount, payment_date) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissd", $customer_id, $order_id, $payment_method, $payment_status, $total_amount);
$stmt->execute();
$stmt->close();

// Update the order status
$sql = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $payment_status, $order_id);
$stmt->execute();
$stmt->close();

// Redirect to account page or a confirmation page
header("Location: account.php");
exit();
?>
