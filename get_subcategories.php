<?php
include 'db_connection.php';

$category_id = $_GET['category'];

$sql = "SELECT * FROM subcategories WHERE category_id = '$category_id'";
$result = $conn->query($sql);

$subcategories = [];
while ($row = $result->fetch_assoc()) {
    $subcategories[] = $row;
}

echo json_encode($subcategories);
?>
