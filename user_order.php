<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

// Retrieve user's orders
$sql = "SELECT * FROM orders WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Orders</title>
</head>
<body>
    <h1>Your Orders</h1>
    <ul>
        <?php foreach ($orders as $order): ?>
            <li>Order ID: <?php echo $order['id']; ?> - Total Amount: <?php echo $order['total_amount']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
