<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}

// Fetch order_id and total_amount from session or request
$order_id = $_GET['order_id'] ?? null;  // Or get it from session or other means
$total_amount = $_GET['total_amount'] ?? null;  // Total amount from order

if (!$order_id || !$total_amount) {
    echo "Invalid order details.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        .payment-details {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Payment</h2>
    <form id="payment_form" method="post" action="payment_process.php">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
            <!-- Add other payment methods as needed -->
        </select>

        <div id="credit_card_details" class="payment-details" style="display: none;">
            <label for="card_number">Card Number:</label>
            <input type="text" name="card_number" id="card_number" maxlength="16" required>
            
            <label for="expiry_date">Expiry Date:</label>
            <input type="text" name="expiry_date" id="expiry_date" maxlength="5" placeholder="MM/YY" required>
            
            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" id="cvv" maxlength="3" required>
        </div>

        <div id="upi_details" class="payment-details" style="display: none;">
            <label for="upi_id">UPI ID:</label>
            <input type="text" name="upi_id" id="upi_id" required>
        </div>

        <div id="net_banking_details" class="payment-details" style="display: none;">
            <label for="bank_account">Bank Account Number:</label>
            <input type="text" name="bank_account" id="bank_account" maxlength="20" required>
            
            <label for="ifsc_code">IFSC Code:</label>
            <input type="text" name="ifsc_code" id="ifsc_code" maxlength="11" required>
        </div>

        <button type="submit">Submit Payment</button>
    </form>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            var method = this.value;
            document.querySelectorAll('.payment-details').forEach(function(div) {
                div.style.display = 'none';
            });
            if (method === 'Credit Card') {
                document.getElementById('credit_card_details').style.display = 'block';
            } else if (method === 'UPI') {
                document.getElementById('upi_details').style.display = 'block';
            } else if (method === 'Net Banking') {
                document.getElementById('net_banking_details').style.display = 'block';
            }
        });
    </script>
</body>
</html>
