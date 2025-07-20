<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch customer details (limited to the available fields)
$sql = "SELECT username FROM customer WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch address details for the customer
$sql = "SELECT * FROM address WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$address = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch combined order and payment status
$sql = "
    SELECT o.order_id, o.order_date, o.total_amount, o.size, o.quantity,
           py.payment_id, py.payment_status AS payment_status, py.total_amount AS payment_amount
    FROM orders o
    LEFT JOIN payments py ON o.order_id = py.order_id
    WHERE o.customer_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$combined_details = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <title>Account Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        h3 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        p {
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color:  #4b2315;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-data {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Account Information</h2>

        <div class="section">
            <h3>Customer Details</h3>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
            <p><strong>First Name:</strong> <?php echo isset($address['firstname']) ? htmlspecialchars($address['firstname']) : 'N/A'; ?></p>
            <p><strong>Last Name:</strong> <?php echo isset($address['lastname']) ? htmlspecialchars($address['lastname']) : 'N/A'; ?></p>
            <p><strong>Mobile:</strong> <?php echo isset($address['mobile']) ? htmlspecialchars($address['mobile']) : 'N/A'; ?></p>
            <p><strong>Area:</strong> <?php echo isset($address['area']) ? htmlspecialchars($address['area']) : 'N/A'; ?></p>
            <p><strong>City:</strong> <?php echo isset($address['city']) ? htmlspecialchars($address['city']) : 'N/A'; ?></p>
            <p><strong>Pincode:</strong> <?php echo isset($address['pincode']) ? htmlspecialchars($address['pincode']) : 'N/A'; ?></p>
            <p><strong>State:</strong> <?php echo isset($address['state']) ? htmlspecialchars($address['state']) : 'N/A'; ?></p>
        </div>

        <div class="section">
            <h3>Order and Payment Status</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Payment ID</th>
                        <th>Payment Status</th>
                        <th>Payment Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($combined_details->num_rows > 0): ?>
                        <?php while ($row = $combined_details->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td>₹<?php echo htmlspecialchars($row['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['size']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_id'] ? $row['payment_id'] : 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_status'] ? $row['payment_status'] : 'N/A'); ?></td>
                            <td>₹<?php echo htmlspecialchars($row['payment_amount'] ? $row['payment_amount'] : '0'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8">No orders or payment history found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
