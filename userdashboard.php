<?php
session_start();
include 'db_connection.php';

// Check if order success message is set
$order_success = isset($_GET['order_success']) && $_GET['order_success'] === 'true';

// Fetch ordered products if order success
if ($order_success && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT products.name, products.price, ordered_products.size, products.image 
            FROM ordered_products 
            INNER JOIN products ON ordered_products.product_id = products.id 
            INNER JOIN orders ON ordered_products.order_id = orders.id 
            WHERE orders.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ordered_products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>
<body>
    <h1>Welcome to the Store</h1>

    <!-- Display ordered products if order success -->
    <?php if ($order_success && isset($ordered_products) && !empty($ordered_products)): ?>
        <h2>Your Ordered Products:</h2>
        <ul>
            <?php foreach ($ordered_products as $product): ?>
                <li>
                    <img src="<?php echo $product['image']; ?>" alt="Product Image" width="100">
                    <p>Name: <?php echo $product['name']; ?></p>
                    <p>Price: $<?php echo $product['price']; ?></p>
                    <p>Size: <?php echo $product['size']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Add other content of the index page here -->
</body>
</html>
