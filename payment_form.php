<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch orders
$sql = "SELECT * FROM orders WHERE customer_id = ? AND payment_status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); }
        h2 { text-align: center; }
        form { margin-top: 20px; }
        label { display: block; margin: 10px 0 5px; }
        select, input[type="text"] { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; }
        input[type="submit"]:hover { background-color: #45a049; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment</h2>
        
        <form action="payment.php" method="get">
            <label for="order_id">Select Order:</label>
            <select id="order_id" name="order_id" required>
                <option value="">Select an Order</option>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <option value="<?php echo $order['order_id']; ?>">Order ID: <?php echo $order['order_id']; ?> - ₹<?php echo $order['total_amount']; ?></option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Proceed to Payment">
        </form>
    </div>
</body>
</html>
