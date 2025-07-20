<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id']) || !isset($_GET['order_id'])) {
    echo "Invalid access.";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql = "SELECT total_amount FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Order not found.";
    exit();
}

$total_amount = $order['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Banking Payment</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h2>Net Banking Payment</h2>

    <form action="process_net_banking_payment.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">

        <label for="bank_account">Bank Account Number:</label>
        <input type="text" id="bank_account" name="bank_account" pattern="\d{12,16}" maxlength="16" required>
        <br>

        <label for="ifsc_code">IFSC Code:</label>
        <input type="text" id="ifsc_code" name="ifsc_code" pattern="[A-Z]{4}\d{7}" maxlength="11" required>
        <br>

        <label for="security_pin">Security PIN:</label>
        <input type="password" id="security_pin" name="security_pin" pattern="\d{4,6}" maxlength="6" required>
        <br>

        <label for="transaction_password">Transaction Password:</label>
        <input type="password" id="transaction_password" name="transaction_password" maxlength="20" required>
        <br>

        <input type="submit" value="Pay Now">
    </form>

</body>
</html>
