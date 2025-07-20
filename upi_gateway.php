<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if (isset($_POST['order_id']) && isset($_POST['upi_id']) && isset($_POST['security_pin'])) {
    $order_id = intval($_POST['order_id']);
    $upi_id = trim($_POST['upi_id']);
    $security_pin = trim($_POST['security_pin']);
    
    // Assuming you have a predefined PIN for validation (in a real scenario, the PIN should be securely stored and verified)
   // $valid_pin = "123456";  // Example valid PIN (replace this with actual secure logic)

    if ($security_pin !== $valid_pin) {
        echo "Invalid Security PIN. Please try again.";
        exit();
    }

    // Simulate payment processing
    $sql = "UPDATE orders SET payment_status = 'Paid' WHERE order_id = ? AND payment_status = 'Processing Payment'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Insert into payments table
        $sql = "INSERT INTO payments (order_id, customer_id, payment_method, amount, payment_status, payment_date) 
                VALUES (?, ?, 'upi', (SELECT total_amount FROM orders WHERE order_id = ?), 'Paid', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $order_id, $_SESSION['customer_id'], $order_id);
        $stmt->execute();

        echo "Payment successful. Your order has been processed.";
        header("Location: account.php");
        exit();
    } else {
        echo "Failed to complete payment. Please try again.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
