<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Check if the admin session variable is set
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Redirect to login page if not admin
    exit();
}

// Fetch all orders from the database along with product and the chosen address details
$query = "SELECT o.order_id, o.customer_id, c.username, o.total_amount, o.order_date, 
                 p.image, a.area, a.city, a.pincode, a.state 
          FROM orders o 
          JOIN customer c ON o.customer_id = c.customer_id 
          JOIN products p ON o.product_id = p.product_id 
          JOIN address a ON o.address_id = a.address_id 
          ORDER BY o.order_date DESC"; // Fetch the address for each order

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4b2315;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        img {
            width: 100px; /* Set the width for product images */
            height: auto;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            background-color: #4b2315;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        a:hover {
            background-color: #3a1a10;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>All Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Username</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Product Image</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td>₹<?php echo htmlspecialchars($order['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($order['image']); ?>" alt="Product Image"></td>
                            <td><?php echo htmlspecialchars($order['area'] . ', ' . $order['city'] . ', ' . $order['state'] . ' - ' . $order['pincode']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="admin.php">Back to Admin Panel</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
