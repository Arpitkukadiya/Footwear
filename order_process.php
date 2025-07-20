<?php
session_start();
include 'db_connection.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['customer_id'])) {
    echo "User ID not set. Please log in first.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve order details from the form
    $customer_id = $_SESSION['customer_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $mobile = $_POST['mobile'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $state = $_POST['state'];

    // Prepare and execute SQL statement to insert the order into the database
    $sql = "INSERT INTO orders (customer_id, firstname, lastname, mobile, area, city, pincode, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $customer_id, $firstname, $lastname, $mobile, $area, $city, $pincode, $state);

    if ($stmt->execute()) {
        // Get the last inserted order ID
        $order_id = $stmt->insert_id;
        $_SESSION['order_id'] = $order_id;

        // Retrieve ordered products from the session and store them in the order_details table
        foreach ($_SESSION['cart'] as $product) {
            if (is_array($product)) {
                $product_id = $product['id'];
                $size = $product['size'];
                $quantity = 1; // Assuming quantity is always 1 for each product in the cart

                $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, size, quantity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisi", $order_id, $product_id, $size, $quantity);
                
                if (!$stmt->execute()) {
                    echo "Error inserting order details: " . $stmt->error;
                    exit(); // Exit if there is an error inserting order details
                }
            }
        }

        // Clear the cart and other relevant session variables
        unset($_SESSION['cart']);

        // Redirect to a thank you page or any other page
        header('Location: order_success.php');
        exit();
    } else {
        // Handle the case where the order couldn't be inserted into the database
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>
