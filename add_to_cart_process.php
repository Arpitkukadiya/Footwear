<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['size'])) {
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];

    // Retrieve product details based on product ID
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Create an array to store product details
        $product = array(
            'id' => $product_id,
            'name' => $row['name'],
            'price' => $row['price'],
            'size' => $size
        );

        // Check if the cart session variable is set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Add product to the cart array
        $_SESSION['cart'][] = $product;

        // Redirect back to the product page with a success message
        header("Location: product.php?id=$product_id&added_to_cart=true");
        exit();
    } else {
        // Redirect back to the product page with an error message
        header("Location: product.php?id=$product_id&added_to_cart=false");
        exit();
    }
} else {
    // Redirect back to the product page if product ID or size is not set
    header("Location: product.php?added_to_cart=false");
    exit();
}
?>
