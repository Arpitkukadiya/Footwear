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
<html>
<head>
    <title>UPI Payment</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h2>UPI Payment</h2>

    <form action="process_upi_payment.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">

        <label for="upi_id">UPI ID:</label>
        <input type="text" id="upi_id" name="upi_id" pattern="[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+" maxlength="50" required>
        <br>

        <label for="upi_amount">Amount:</label>
        <input type="text" id="upi_amount" name="upi_amount" pattern="\d+(\.\d{2})?" maxlength="10" required value="<?php echo htmlspecialchars($total_amount); ?>">
        <br>

        <label for="security_pin">Security PIN:</label>
        <input type="password" id="security_pin" name="security_pin" maxlength="6" pattern="\d{4,6}" required>
        <br>

        <input type="submit" value="Pay Now">
    </form>

</body>
</html>
