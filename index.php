<?php
session_start();

include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Footwear Store</title>
    <link rel="stylesheet" type="text/css" href="style11.css">
</head>
<style>
    body {
        background-image: url('shoessuhani.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed; 
        background-size: 100% 100%;
    }
</style>
<div class="background-image"></div>
<div class="content">
    <div class="navbar">
        <h1>Elegance</h1>
        <div class="categories">
            <ul class="category-menu">
                <li><a href="category.php?category=men">Men</a></li>
                <li><a href="category.php?category=women">Women</a></li>
                <li><a href="category.php?category=kids">Kids</a></li>
                <li><a href="login.php">Admin</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="order.php">orders</a></li>
                
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="account.php">Account</a></li>
                    <li>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Display welcome message if it exists -->
    <?php if (isset($_SESSION['welcome_message'])): ?>
        <div style="margin: 20px; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
            <?php
            echo htmlspecialchars($_SESSION['welcome_message']);
            unset($_SESSION['welcome_message']); // Clear the message after displaying it
            ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
