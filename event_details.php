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

// Fetch tickets for the event
$ticket_sql = "SELECT * FROM tickets WHERE event_id = $id";
$ticket_result = $conn->query($ticket_sql);
$tickets = [];
if ($ticket_result->num_rows > 0) {
    while ($row = $ticket_result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['event_title']); ?></title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/index.css">
    <script src="https://js.paystack.co/v1/inline.js"></script> <!-- Paystack JS -->
    <style>
        h1 {
            color: #333;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        button:hover {
            background-color: #218838;
        }

        .total-price {
            font-weight: bold;
            margin-top: 10px;
        }

        .ticket-select {
            display: flex;
            flex-direction: column;
        }

        .ticket-select label {
            margin-top: 10px;
        }
    </style>
    <script>
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.ticket_qty').forEach(input => {
                let qty = parseInt(input.value) || 0;
                let price = parseFloat(input.dataset.price) || 0;
                total += qty * price;
            });
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
    <style>
        .event_hero {
            background: #200122;
            /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #6f0000, #200122);
            /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #6f0000, #200122);
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            color: #FFF;
            height: 60vh;
            position: relative;
        }

        .event_hero img {
            width: 200px;
            height: 200px;
            position: absolute;
            right: 5%;
            top: 30%;
            object-fit: cover;

        }

        .events_hero_text {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60vh;
            position: absolute;
            padding-left: 5%;



        }

        .events_hero_text h1 {
            color: #FFF;
        }
        .events_alls{
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 30px;
            padding: 0 5%;
            margin-top: 50px;
        }
        .event_detals_info{
            background-color: #F1F1F1;
            padding: 0 5%;
            padding-block: 20px;
        }
    </style>
    <?php include 'navbar.php' ?>
    <section>
        <div class="event_hero">
            <div class="events_hero_text">
                <h1><?php echo htmlspecialchars($event['event_title']); ?></h1>
            </div>
            <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['event_title']); ?>">
        </div>
    </section>
    <section>
        <div class="events_alls">
          

            <form id="payment_form" onsubmit="event.preventDefault(); payWithPaystack();">
                <h2>Select Tickets</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Ticket Name</th>
                            <th>Price (GHS)</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)) : ?>
                            <?php foreach ($tickets as $ticket) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ticket['ticket_name']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['ticket_price']); ?></td>
                                    <td>
                                        <input type="number" class="ticket_qty" data-price="<?php echo htmlspecialchars($ticket['ticket_price']); ?>" value="0" min="0" onchange="calculateTotal()">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">No tickets available for this event.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <p class="total-price">Total Price: <span id="total_price">GHS 0.00</span></p>
                <button type="submit">Pay with Paystack</button>
            </form>
            <div class="event_detals_info">
          <p><?php echo htmlspecialchars($event['event_msg']); ?></p>
            <p><i class="fa-solid fa-calendar-days"></i> <?php echo htmlspecialchars($event['event_date']); ?> </p>
            <p><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($event['event_time']); ?></p>
            <p><i class="fa-solid fa-ticket"></i> <?php echo htmlspecialchars($event['event_price']); ?></p>
          </div>
        </div>
    </section>

</body>

</html>