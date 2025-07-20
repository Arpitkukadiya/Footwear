<?php
session_start();
include 'db_connection.php';

$errors = []; // Initialize an empty array to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $mobile = trim($_POST['mobile']);
    $area = trim($_POST['area']);
    $city = trim($_POST['city']);
    $pincode = trim($_POST['pincode']);
    $state = trim($_POST['state']);

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($firstname)) {
        $errors[] = "First name is required.";
    }
    if (empty($lastname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($mobile)) {
        $errors[] = "Mobile number is required.";
    }
    if (empty($area)) {
        $errors[] = "Area is required.";
    }
    if (empty($city)) {
        $errors[] = "City is required.";
    }
    if (empty($pincode)) {
        $errors[] = "Pincode is required.";
    }
    if (empty($state)) {
        $errors[] = "State is required.";
    }

    // Check for existing user
    if (empty($errors)) {
        $check_user = "SELECT * FROM customer WHERE username = ?";
        $stmt = $conn->prepare($check_user);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username already exists.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_user = "INSERT INTO customer (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_user);
            $stmt->bind_param("ss", $username, $hashed_password);
            $stmt->execute();

            // Get customer ID
            $customer_id = $conn->insert_id;

            // Insert address
            $insert_address = "INSERT INTO address (customer_id, firstname, lastname, mobile, area, city, pincode, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_address);
            $stmt->bind_param("isssssss", $customer_id, $firstname, $lastname, $mobile, $area, $city, $pincode, $state);
            $stmt->execute();

            $_SESSION['username'] = $username; // Set session variable
            $_SESSION['customer_id'] = $customer_id; // Store customer ID for future use
            $_SESSION['success_message'] = "Registration successful! Welcome, $username!";
            header("Location: index.php"); // Redirect to homepage
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
   
    <style>
     body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4; /* Light background for better contrast */
    }

    .registration-form {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: white; /* White background for the form */
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    .registration-form h2 {
        text-align: center;
        color: #333; /* Darker text color */
    }

    .registration-form label {
        display: block;
        margin-bottom: 8px;
        color: #555; /* Medium grey for labels */
    }

    .registration-form input[type="text"],
    .registration-form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc; /* Light border for inputs */
        border-radius: 4px; /* Rounded corners */
    }

    .registration-form input[type="text"]:focus,
    .registration-form input[type="password"]:focus {
        border-color: #007BFF; /* Blue border on focus */
        outline: none; /* Remove default outline */
    }

    .registration-form input[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #4b2315; /* Blue background for the button */
        color: white; /* White text color for button */
        border: none; /* Remove border */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor on hover */
        font-size: 16px; /* Larger font size */
    }

    .registration-form .error-messages {
        margin-bottom: 20px;
        background-color: #f8d7da; /* Light red background for error messages */
        color: #721c24; /* Dark red text color */
        padding: 10px;
        border: 1px solid #f5c6cb; /* Red border */
        border-radius: 5px; /* Rounded corners */
    }

    .registration-form .success-message {
        background-color: #d4edda; /* Light green background for success messages */
        color: #155724; /* Dark green text color */
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb; /* Green border */
        border-radius: 5px; /* Rounded corners */
    }

    .navbar {
        background-color: #333; /* Dark background for navbar */
        color: white; /* White text for navbar */
        padding: 15px;
    }

    .navbar h1 {
        margin: 0; /* Remove default margin */
    }

    .category-menu {
        list-style: none; /* Remove bullets from list */
        padding: 0; /* Remove default padding */
        display: flex; /* Use flexbox for horizontal alignment */
        justify-content: space-around; /* Space out items */
    }

    .category-menu li a {
        color: white; /* White text for links */
        text-decoration: none; /* Remove underline from links */
    }

    .category-menu li a:hover {
        color: #007BFF; /* Change color on hover */
    }
    </style>
</head>
<body>
    <div class="registration-form">
        <h2>Register</h2>

        <!-- Display errors if they exist -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" id="firstname" required>

            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" id="lastname" required>

            <label for="mobile">Mobile:</label>
            <input type="text" name="mobile" id="mobile" required>

            <label for="area">Area:</label>
            <input type="text" name="area" id="area" required>

            <label for="city">City:</label>
            <input type="text" name="city" id="city" required>

            <label for="pincode">Pincode:</label>
            <input type="text" name="pincode" id="pincode" required>

            <label for="state">State:</label>
            <input type="text" name="state" id="state" required>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
