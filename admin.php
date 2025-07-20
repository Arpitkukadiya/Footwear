<?php
session_start(); // Start the session

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php'); // Redirect if not logged in as admin
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color:#fff;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #4b2315;
            padding: 15px;
            position: fixed;
            height: 100%;
            color: #fff;
        }

        .sidebar h1 {
            font-size: 1.5em;
            margin-top: 0;
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin: 15px 0;
        }

        .sidebar a {
            text-decoration: none;
            color: #fff;
            font-size: 1.1em;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar a:active {
            background-color: #444;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .main-content h1 {
            color: #0a0a0a;
            margin: 0;
            padding: 20px 0;
            background-color:#fff;
            color: #4b2315;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        
        <ul>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="remove_product.php">Remove Product</a></li>
            <li><a href="view_user.php">View Users</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="logout.php">Logout</a></li>

        </ul>
    </div>
    <div class="main-content">
        <h1>Welcome to Admin Panel</h1>
    </div>
</body>
</html>
