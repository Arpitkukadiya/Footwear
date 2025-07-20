<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

$errors = [];
$firstname = $lastname = $mobile = $area = $city = $pincode = $state = "";
$total_price = 0;

// Check if the cart is set and not empty
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<h2>Cart</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Product Name</th><th>Price</th><th>Size</th><th>Image</th></tr>";

    foreach ($_SESSION['cart'] as $product) {
        if (is_array($product)) {
            $product_id = $product['id'];
            $size = $product['size'];
          //  $quantity = $product['quantity']; // Get quantity from cart item

            // Retrieve product details from the database
            $sql = "SELECT * FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                echo "<td>₹" . htmlspecialchars($row['price']) . "</td>";
                echo "<td>" . htmlspecialchars($size) . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='Product Image' width='100'></td>";
                echo "</tr>";
              //  $total_price += $row['price'] * $quantity; // Calculate total price for the cart
            } else {
                echo "<tr><td colspan='4'>Product not found in database.</td></tr>";
            }

            $stmt->close();
        }
    }

    echo "</table>";
    echo "<p>Total Price: ₹" . $total_price . "</p>";

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $mobile = trim($_POST['mobile']);
        $area = trim($_POST['area']);
        $city = trim($_POST['city']);
        $pincode = trim($_POST['pincode']);
        $state = trim($_POST['state']);

        // Validation
        if (empty($firstname) || !preg_match("/^[a-zA-Z]+$/", $firstname)) {
            $errors['firstname'] = "Invalid first name.";
        }

        if (empty($lastname) || !preg_match("/^[a-zA-Z]+$/", $lastname)) {
            $errors['lastname'] = "Invalid last name.";
        }

        if (empty($mobile) || !preg_match("/^[0-9]{10}$/", $mobile)) {
            $errors['mobile'] = "Invalid mobile number.";
        }

        if (empty($area)) {
            $errors['area'] = "Area cannot be empty.";
        }

        if (empty($city) || !preg_match("/^[a-zA-Z]+$/", $city)) {
            $errors['city'] = "Invalid city.";
        }

        if (empty($pincode) || !preg_match("/^[0-9]{6}$/", $pincode)) {
            $errors['pincode'] = "Invalid pincode.";
        }

        if (empty($state)) {
            $errors['state'] = "State cannot be empty.";
        }

        // If there are no errors, process the order
        if (empty($errors)) {
            $conn->begin_transaction();

            try {
                // Insert order into orders table
                $sql = "INSERT INTO orders (customer_id, total_amount, order_date) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $_SESSION['customer_id'], $total_price);
                $stmt->execute();
                $order_id = $stmt->insert_id; // Get the last inserted order ID
                $stmt->close();

                // Insert address into address table
                $sql = "INSERT INTO address (customer_id, firstname, lastname, mobile, area, city, pincode, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssss", $_SESSION['customer_id'], $firstname, $lastname, $mobile, $area, $city, $pincode, $state);
                $stmt->execute();
                $stmt->close();

                // Insert order details into order_details table
                foreach ($_SESSION['cart'] as $product) {
                    if (is_array($product)) {
                        $product_id = $product['id'];
                        $size = $product['size'];
                        $quantity = $product['quantity']; // Get quantity from cart item

                        // Check if product exists
                        $sql = "SELECT product_id, price FROM products WHERE product_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $price = $row['price'];

                            // Insert into order_details
                            $sql = "INSERT INTO order_details (order_id, product_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("iiiii", $order_id, $product_id, $size, $quantity, $price);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            throw new Exception("Product with ID $product_id does not exist.");
                        }
                    }
                }

                $conn->commit();
                unset($_SESSION['cart']); // Clear the cart after successful order
                header("Location: order_success.php");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                echo "Order failed: " . $e->getMessage();
            }
        }
    }

    // Display the order form with errors
    ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, .error {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        input[type="submit"] {
            background: #28a745;
            color: white;
            cursor: pointer;
        }
    </style>
   
    <form id="orderFormDetails" action="" method="post">
        <label>First Name:</label>
        <input type="text" name="firstname" value="<?= htmlspecialchars($firstname) ?>" required>
        <span class="error"><?= $errors['firstname'] ?? '' ?></span>

        <label>Last Name:</label>
        <input type="text" name="lastname" value="<?= htmlspecialchars($lastname) ?>" required>
        <span class="error"><?= $errors['lastname'] ?? '' ?></span>

        <label>Mobile:</label>
        <input type="text" name="mobile" maxlength="10" pattern="[0-9]{10}" value="<?= htmlspecialchars($mobile) ?>" required>
        <span class="error"><?= $errors['mobile'] ?? '' ?></span>

        <label>Area:</label>
        <input type="text" name="area" value="<?= htmlspecialchars($area) ?>" required>
        <span class="error"><?= $errors['area'] ?? '' ?></span>

        <label>City:</label>
        <input type="text" name="city" value="<?= htmlspecialchars($city) ?>" required>
        <span class="error"><?= $errors['city'] ?? '' ?></span>

        <label>Pincode:</label>
        <input type="text" name="pincode" maxlength="6" pattern="[0-9]{6}" value="<?= htmlspecialchars($pincode) ?>" required>
        <span class="error"><?= $errors['pincode'] ?? '' ?></span>

        <label>State:</label>
        <input type="text" name="state" value="<?= htmlspecialchars($state) ?>" required>
        <span class="error"><?= $errors['state'] ?? '' ?></span>

        <input type="submit" value="Place Order">
    </form>
    <?php
} else {
    echo "<p>Your cart is empty.</p>";
}

$conn->close();
?>
