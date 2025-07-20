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
    <title>Card Payment</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h2>Card Payment</h2>

    <form action="process_card_payment.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">

        <label for="card_number">Card Number:</label>
        <input type="text" id="card_number" name="card_number" pattern="\d{16}" maxlength="16" required>
        <br>

        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" pattern="\d{3}" maxlength="3" required>
        <br>

        <label for="expiry_date">Expiry Date (MM/YY):</label>
        <input type="text" id="expiry_date" name="expiry_date" pattern="\d{2}/\d{2}" maxlength="5" required>
        <br>

        <input type="submit" value="Pay Now">
    </form>

</body>
</html>
