<?php
session_start();

// Database connection
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: login.php'); // Redirect if not logged in as admin
    exit();
}

// Retrieve users from the database
$sql = "SELECT c.customer_id, c.username, a.firstname, a.lastname, a.mobile, 
               COUNT(o.order_id) AS total_orders
        FROM customer c 
        LEFT JOIN address a ON c.customer_id = a.customer_id 
        LEFT JOIN orders o ON c.customer_id = o.customer_id 
        GROUP BY c.customer_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
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
    <h2>User Information</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Mobile</th>
            <th>Total Orders</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['firstname']); ?></td>
            <td><?php echo htmlspecialchars($row['lastname']); ?></td>
            <td><?php echo htmlspecialchars($row['mobile']); ?></td>
            <td><?php echo htmlspecialchars($row['total_orders']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="admin.php">Back to Admin Panel</a>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
