<?php
session_start();
include 'db_connection.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

// Retrieve the customer ID from the session
$customer_id = $_SESSION['customer_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $errors = [];

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $mobile = trim($_POST['mobile']);
    $area = trim($_POST['area']);
    $city = trim($_POST['city']);
    $pincode = trim($_POST['pincode']);
    $state = trim($_POST['state']);
    $total_amount = trim($_POST['total_amount']);
    $products = $_POST['products'];
    $sizes = $_POST['sizes'];

    if (empty($firstname) || !preg_match("/^[a-zA-Z]+$/", $firstname)) {
        $errors[] = "Invalid first name.";
    }

    if (empty($lastname) || !preg_match("/^[a-zA-Z]+$/", $lastname)) {
        $errors[] = "Invalid last name.";
    }

    if (empty($mobile) || !preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Invalid mobile number.";
    }

    if (empty($area)) {
        $errors[] = "Area cannot be empty.";
    }

    if (empty($city) || !preg_match("/^[a-zA-Z]+$/", $city)) {
        $errors[] = "Invalid city.";
    }

    if (empty($pincode) || !preg_match("/^[0-9]{6}$/", $pincode)) {
        $errors[] = "Invalid pincode.";
    }

    if (empty($state) || !preg_match("/^[a-zA-Z]+$/", $state)) {
        $errors[] = "Invalid state.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        echo "<p><a href='cart.php'>Go back to the cart</a></p>";
        exit();
    }

    // Insert the order into the orders table
    $sql = "INSERT INTO orders (customer_id, order_date, total_amount, firstname, lastname, mobile, area, city, pincode, state)
            VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }
    $stmt->bind_param("idsssssss", $customer_id, $total_amount, $firstname, $lastname, $mobile, $area, $city, $pincode, $state);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert each product into the order_details table
        $sql = "INSERT INTO order_details (order_id, product_id, size, quantity) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "Prepare failed: " . $conn->error;
            exit();
        }

        for ($i = 0; $i < count($products); $i++) {
            $product_id = $products[$i];
            $size = $sizes[$i];
            $stmt->bind_param("iii", $order_id, $product_id, $size);
            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->error;
                exit();
            }
        }

        // Clear the cart
        unset($_SESSION['cart']);
        echo "<p style='color:green;'>Order placed successfully.</p>";
    } else {
        echo "Execute failed: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>Order Confirmation</h2>
    <p>Your order has been placed successfully. Thank you for shopping with us!</p>
    <a href="index.php">Continue Shopping</a>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
