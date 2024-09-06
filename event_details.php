<?php
include 'db.php';

$id = $_GET['id'];

if (!$id || !is_numeric($id)) {
    echo "Invalid event ID";
    exit;
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    echo "Event not found";
    exit;
}

// Fetch tickets for the event
$ticket_stmt = $conn->prepare("SELECT * FROM tickets WHERE event_id = ?");
$ticket_stmt->bind_param("i", $id);
$ticket_stmt->execute();
$ticket_result = $ticket_stmt->get_result();
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
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/event_details.css">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.ticket_qty').forEach(input => {
                let qty = parseInt(input.value) || 0;
                let price = parseFloat(input.dataset.price) || 0;
                total += qty * price;
            });
            document.getElementById('total_price').textContent = `GHS ${total.toFixed(2)}`;
            return total;
        }

        function payWithPaystack() {
    const email = document.getElementById('email').value;
    const fullName = document.getElementById('full_name').value;

    if (!email || !fullName) {
        alert('Please provide your full name and email address.');
        return;
    }

    let totalAmount = calculateTotal() * 100; // Paystack requires amount in kobo/pesewas
    const ticketQuantities = [];
    const ticketsPurchased = [];

    document.querySelectorAll('.ticket_qty').forEach(input => {
        let qty = parseInt(input.value) || 0;
        let ticketName = input.dataset.name;
        let price = parseFloat(input.dataset.price) || 0;
        let ticketId = input.dataset.ticketId;

        for (let i = 0; i < qty; i++) {
            const ref = 'ref_' + Math.floor((Math.random() * 1000000000) + 1);
            ticketQuantities.push({ticketName, price, ref});
            ticketsPurchased.push({ticketId, ref});
        }
    });

    if (ticketQuantities.length === 0) {
        alert('Please select at least one ticket.');
        return;
    }

    let handler = PaystackPop.setup({
        key: 'pk_test_112a19f8ae988db1be016b0323b0e4fe95783fe8', // Replace with your public key
        email: email,
        amount: totalAmount, // in pesewas
        currency: 'GHS',
        callback: function(response) {
            console.log('Payment successful:', response);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "save_purchase.php", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    let data = JSON.parse(xhr.responseText);
                    console.log('Server response:', data);

                    if (data.status === 'success') {
                        // Redirect to the view_references.php with reference numbers
                        let redirectUrl = `view_references.php?references=${encodeURIComponent(data.references)}`;
                        window.location.href = redirectUrl;
                    } else {
                        alert('Error saving purchase details: ' + data.message);
                    }
                } else {
                    alert('Server error: ' + xhr.statusText);
                }
            };

            xhr.send(JSON.stringify({
                fullName: fullName,
                email: email,
                tickets: ticketsPurchased,
                paystackRef: response.reference
            }));
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
    <?php include 'navbar.php'; ?>
    <section>
        <div class="event_hero" style="background-image: url('uploads/<?php echo htmlspecialchars($event['image']); ?>');">
            <div class="events_hero_text">
                <h1><?php echo htmlspecialchars($event['event_title']); ?></h1>
            </div>
            <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['event_title']); ?>">
        </div>
    </section>

    <section>
        <div class="events_alls">
            <div class="events_table">
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
                                            <input type="number" class="ticket_qty" data-ticket-id="<?php echo $ticket['id']; ?>" data-price="<?php echo htmlspecialchars($ticket['ticket_price']); ?>" data-name="<?php echo htmlspecialchars($ticket['ticket_name']); ?>" value="0" min="0" onchange="calculateTotal()">
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

                    <div class="total-price">
                        <p>Total Price: <br></p>
                        <span id="total_price">GHS 0.00</span>
                    </div>

                    <div class="user_details">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required><br>

                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" required><br>
                    </div>

                    <div class="pay">
                        <button type="submit">Pay with Paystack</button>
                    </div>
                </form>
            </div>
            <div class="event_details_info">
                <h3><?php echo htmlspecialchars($event['event_title']); ?></h3>
                <hr>
                <p><?php echo htmlspecialchars($event['event_msg']); ?></p>
                <hr>
                <p><i class="fa-solid fa-calendar-days"></i> <?php echo htmlspecialchars($event['event_date']); ?></p>
                <p><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($event['event_time']); ?></p>
                <p><i class="fa-solid fa-ticket"></i> <?php echo htmlspecialchars($event['event_price']); ?></p>
                <hr>
            </div>
        </div>
    </section>
</body>

</html>
