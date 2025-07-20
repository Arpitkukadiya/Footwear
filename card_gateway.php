<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

if (isset($_POST['order_id']) && isset($_POST['payment_method']) && isset($_POST['card_number']) && isset($_POST['card_expiry']) && isset($_POST['card_cvc'])) {
    $order_id = intval($_POST['order_id']);
    $payment_method = trim($_POST['payment_method']);
    $card_number = trim($_POST['card_number']);
    $card_expiry = trim($_POST['card_expiry']);
    $card_cvc = trim($_POST['card_cvc']);

    // Simulate payment processing
    $sql = "UPDATE orders SET payment_status = 'Paid' WHERE order_id = ? AND payment_status = 'Processing Payment'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Insert into payments table
        $sql = "INSERT INTO payments (order_id, customer_id, payment_method, amount, payment_status, payment_date) VALUES (?, ?, ?, (SELECT total_amount FROM orders WHERE order_id = ?), 'Paid', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $order_id, $_SESSION['customer_id'], $payment_method, $order_id);
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
