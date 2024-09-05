<?php
include 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM events WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    echo "Event not found";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event['event_title']; ?></title>
    <script src="https://js.paystack.co/v1/inline.js"></script> <!-- Paystack JS -->
    <script>
        function calculateTotal() {
            let qty = document.getElementById('ticket_qty').value;
            let price = <?php echo $event['event_price']; ?>;
            let total = qty * price;
            document.getElementById('total_price').textContent = 'GHS ' + total.toFixed(2);
            return total;
        }

        function payWithPaystack() {
            let totalAmount = calculateTotal() * 100; // Paystack requires amount in kobo/pesewas

            let handler = PaystackPop.setup({
                key: 'pk_test_112a19f8ae988db1be016b0323b0e4fe95783fe8', // Replace with your public key
                email: 'customer@example.com', // Change to dynamic customer email
                amount: totalAmount, // in pesewas (100 GHS = 10000 pesewas)
                currency: 'GHS',
                ref: '' + Math.floor((Math.random() * 1000000000) + 1), // Generate a random reference number
                callback: function(response) {
                    alert('Payment successful! Reference: ' + response.reference);
                    // Here you can handle the backend logic to save the payment
                },
                onClose: function() {
                    alert('Payment window closed.');
                }
            });
            handler.openIframe();
        }
    </script>
</head>
<body>

    <h1><?php echo $event['event_title']; ?></h1>
    <img src="uploads/<?php echo $event['image']; ?>" alt="<?php echo $event['event_title']; ?>">
    <p><?php echo $event['event_msg']; ?></p>
    <p>Date: <?php echo $event['event_date']; ?> Time: <?php echo $event['event_time']; ?></p>
    <p>Price per ticket: GHS <?php echo $event['event_price']; ?></p>

    <label for="ticket_qty">Select Quantity:</label>
    <input type="number" id="ticket_qty" name="ticket_qty" value="1" min="1" onchange="calculateTotal()" required><br><br>

    <p>Total Price: <span id="total_price">GHS <?php echo $event['event_price']; ?></span></p>

    <button type="button" onclick="payWithPaystack()">Pay with Paystack</button>

</body>
</html>
