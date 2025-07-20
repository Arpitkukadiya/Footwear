<!DOCTYPE html>
<html>
<head>
    <title>Add to Cart</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h1>Add to Cart</h1>
    <?php
    // Start session to access cart data
    session_start();

    // Check if the product ID and size are set in the POST request
    if (isset($_POST['product_id']) && isset($_POST['size'])) {
        // Retrieve product ID and size from the POST request
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];

        // Retrieve product details from the database based on the product ID
        include 'db_connection.php';

        // Ensure to use the correct column names
        $sql = "SELECT product_id, product_Name, price FROM products WHERE product_id = '$product_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Add product to cart session variable
            $product = array(
                'id' => $row['product_id'],
                'name' => $row['product_Name'],
                'price' => $row['price'],
                'size' => $size
            );

            // Initialize cart if not already done
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }

            // Add product to cart
            $_SESSION['cart'][] = $product;
            echo "Product added to cart successfully!";
        } else {
            echo "Product not found";
        }
    } else {
        echo "Product ID or size is not set";
    }

    // Close database connection
    $conn->close();
    ?>
    <br><br>
    <a href="cart.php">View Cart</a> <!-- Link to view the cart -->
</body>
</html>
