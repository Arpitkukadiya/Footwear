<?php
session_start();
include 'db_connection.php';

// Fetch the customer's orders to display here
$customer_id = $_SESSION['customer_id'];
$sql = "SELECT o.order_id, o.product_id, o.total_amount, o.size, o.quantity, o.order_date, p.product_Name, p.image 
        FROM orders o 
        JOIN products p ON o.product_id = p.product_id 
        WHERE o.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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
        <h2>Your Order History</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Total Amount</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['product_Name']; ?></td>
                        <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['product_Name']; ?>"></td>
                        <td><?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                        <td>
                            <form action="cancel_order.php" method="post">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <button type="submit">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
