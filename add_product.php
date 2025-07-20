<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_Name = $_POST['product_Name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    
    // Image upload handling
    $image = $_FILES['image']; // Get the uploaded file
    
    // Define a target directory for image uploads
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        $_SESSION['message'] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($image["size"] > 5000000) {
        $_SESSION['message'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['message'] = "Your file was not uploaded.";
    } else {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO products (product_Name, price, category_id, subcategory_id, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiis", $product_Name, $price, $category_id, $subcategory_id, $target_file); // Store the file path

            if ($stmt->execute()) {
                $_SESSION['message'] = "Product added successfully!";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }

            $stmt->close();
            header("Location: add_product.php");
            exit();
        } else {
            $_SESSION['message'] = "Sorry, there was an error uploading your file.";
        }
    }
}

// Retrieve categories for the dropdown
$categories = $conn->query("SELECT * FROM categories");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style>
        /* Styles for the page */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center; /* Center align content */
        }

        h1 {
            font-size: 30px; /* Increased font size for better visibility */
            color: #4b2315;
            margin-bottom: 20px;
            border-bottom: 2px solid #4b2315; /* Underline for emphasis */
            padding-bottom: 10px; /* Spacing below the heading */
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block; /* Block display for labels */
            text-align: left; /* Align labels to the left */
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: calc(100% - 20px); /* Adjusted for padding */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="submit"],
        .back-button {
            width: 100%;
            background-color: #4b2315;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover,
        .back-button:hover {
            background-color: #3d1b14;
        }

        .message {
            background-color: #4b2315;
            padding: 10px;
            color: #fff;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>

    <script>
        function fetchSubcategories(categoryId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_subcategories.php?category=' + categoryId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    var subcategories = JSON.parse(this.responseText);
                    var subcategorySelect = document.getElementById('subcategory_id');
                    subcategorySelect.innerHTML = '';

                    subcategories.forEach(function(subcategory) {
                        var option = document.createElement('option');
                        option.value = subcategory.subcategory_id;
                        option.textContent = subcategory.subcategory_name;
                        subcategorySelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1>Add New Product</h1> <!-- Changed heading for clarity -->

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data"> <!-- Add enctype for file upload -->
            <label for="product_Name">Product Name:</label>
            <input type="text" name="product_Name" required>

            <label for="price">Price:</label>
            <input type="number" name="price" required>

            <label for="category_id">Category:</label>
            <select name="category_id" id="category_id" required onchange="fetchSubcategories(this.value)">
                <option value="">Select Category</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['category_id']); ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="subcategory_id">Subcategory:</label>
            <select name="subcategory_id" id="subcategory_id" required>
                <option value="">Select Subcategory</option>
                <!-- Subcategories will be populated dynamically -->
            </select>

            <label for="image">Image:</label>
            <input type="file" name="image" accept="image/*" required> <!-- File input for image -->

            <input type="submit" value="Add Product">
        </form>

        <!-- Back to Admin Panel Button -->
        <form action="admin.php" method="get">
            <input type="submit" class="back-button" value="Back to Admin Panel">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
