<?php
session_start();
include 'db_connection.php';

// Retrieve products along with their categories and subcategories
$sql = "SELECT p.product_id, p.product_Name, p.price, p.image, c.category_name, s.subcategory_name
        FROM products p
        INNER JOIN categories c ON p.category_id = c.category_id
        INNER JOIN subcategories s ON p.subcategory_id = s.subcategory_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Products</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h1>Remove Products</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php
            echo htmlspecialchars($_SESSION['message']);
            unset($_SESSION['message']); // Clear the message after displaying
            ?>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_Name']); ?></td>
                    <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['subcategory_name']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                    </td>
                    <td>
                        <form action="remove_product_process.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>">
                            <input type="submit" class="remove-btn" value="Remove">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No products found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <form action="admin.php" method="get">
            <input type="submit" class="back-button" value="Back to Admin Panel">
        </form>
    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
