<?php
session_start();
include 'db_connection.php';

// Check if the 'category' parameter is set in the URL
if (isset($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);

    // Corrected SQL query with appropriate column names
    $sql = "SELECT subcategories.subcategory_id AS subcategory_id, subcategories.subcategory_name AS subcategory_name, products.*
            FROM subcategories
            JOIN products ON subcategories.subcategory_id = products.subcategory_id
            WHERE subcategories.category_id = (SELECT categories.category_id FROM categories WHERE categories.category_name = '$category')";

    $result = $conn->query($sql);

    if (!$result) {
        header('Location: error.php'); // Redirect to an error page
        exit(); // Terminate script execution
    }
} else {
    // Redirect the user to an error page or display a message
    header('Location: error.php'); // Redirect to an error page
    exit(); // Terminate script execution
}

// Check if the add to cart form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['size'])) {
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];

    // Add product to cart session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add product to cart array
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'size' => $size
    ];

    // Redirect to cart.php
    header('Location: cart.php');
    exit(); // Stop further execution after redirection
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>category <?php echo ucfirst($category); ?></title>
    <link rel="stylesheet" type="text/css" href="category1.css">
</head>
<body>
    <h1><?php echo ucfirst($category); ?></h1>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product" data-product-id="<?php echo $row['product_id']; ?>">
                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['product_name']; ?>">
                <div class="product-details">
                    <p><?php echo $row['product_name']; ?></p>
                    <p>Price: ₹<?php echo $row['price']; ?></p>
                </div>
                <!-- Display size options -->
                <div id="sizeOptions_<?php echo $row['product_id']; ?>" class="size-options">
                    <form action="" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <label for="size_<?php echo $row['product_id']; ?>" class="size-label">Size:</label>
                        <select name="size" id="size_<?php echo $row['product_id']; ?>" onchange="updateSizeSelection(<?php echo $row['product_id']; ?>);">
                        <div class="select-container">
    
                             <option value="">Select Size</option>
                            <option value="6">uk6</option>
                            <option value="7">uk7</option>
                            <option value="8">uk8</option>
                            <option value="9">uk9</option>
                            <option value="10">uk10</option>
                        </select>
                      
                        <input type="submit" class="add-to-cart" id="addToCart_<?php echo $row['product_id']; ?>" value="Add to Cart" disabled>
                    </form>
                </div>
                <!-- Button to show size options -->
                <button class="show-size-button" onclick="toggleSizeOptions(<?php echo $row['product_id']; ?>);">SIZE</button>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        // Function to toggle size options for the clicked product and collapse others
        function toggleSizeOptions(productId) {
            var products = document.querySelectorAll('.product');
            products.forEach(function(product) {
                var sizeOptions = product.querySelector('.size-options');
                var showSizeButton = product.querySelector('.show-size-button');
                if (productId == product.dataset.productId) {
                    // Expand the clicked product
                    sizeOptions.style.display = 'block';
                    sizeOptions.style.maxHeight = sizeOptions.scrollHeight + 'px'; // Smooth expand
                    showSizeButton.style.display = 'none';
                } else {
                    // Collapse other products
                    sizeOptions.style.maxHeight = '0';
                    setTimeout(function() {
                        sizeOptions.style.display = 'none';
                    }, 300); // Delay hiding to allow transition
                    showSizeButton.style.display = 'block';
                }
            });
        }

        // Function to enable the Add to Cart button if a size is selected
        function updateSizeSelection(productId) {
            var sizeSelect = document.getElementById("size_" + productId);
            var addToCartButton = document.getElementById("addToCart_" + productId);
            addToCartButton.disabled = !sizeSelect.value;
        }
    </script>
</body>
</html>
