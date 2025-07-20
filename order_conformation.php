<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Fetch payment details
$sql = "SELECT * FROM payments WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); }
        h2 { text-align: center; }
        p { line-height: 1.5; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Confirmation</h2>
        <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo $payment['payment_method']; ?></p>
        <p><strong>Transaction ID:</strong> <?php echo $payment['transaction_id']; ?></p>
        <p><strong>Total Amount Paid:</strong> ₹<?php echo $payment['amount']; ?></p>
        <p>Your order has been successfully placed and payment has been completed.</p>
    </div>
</body>
</html>
