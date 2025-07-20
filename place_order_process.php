<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $mobile = $_POST['mobile'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $state = $_POST['state'];

    // Calculate total price
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product) {
        if (is_array($product)) {
            $total_price += $product['price'];
        }
    }

    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        // Insert order details into the database
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO orders (user_id, total_amount, firstname, lastname, mobile, area, city, pincode, state) VALUES ('$user_id', '$total_price', '$firstname', '$lastname', '$mobile', '$area', '$city', '$pincode', '$state')";
        if ($conn->query($sql) === TRUE) {
            echo "Order placed successfully!";
            // Clear the cart after placing the order
            unset($_SESSION['cart']);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "User not logged in. Please log in to place an order.";
    }
} else {
    echo "Invalid request.";
}
?>
