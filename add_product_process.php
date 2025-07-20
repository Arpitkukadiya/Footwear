<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $product_Name = $conn->real_escape_string($_POST['product_Name']);
    $price = intval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    
    // Handle image upload
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_folder = 'uploads/' . $image;
    move_uploaded_file($image_tmp, $image_folder);

    // Handle sizes
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';

    // Insert into products table
    $stmt = $conn->prepare("INSERT INTO products (product_Name, price, category_id, subcategory_id, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('siiss', $product_Name, $price, $category_id, $subcategory_id, $image_folder);
    
    if ($stmt->execute()) {
        echo "Product added successfully!";
        header('Location: admin.php'); // Redirect to admin panel after success
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
