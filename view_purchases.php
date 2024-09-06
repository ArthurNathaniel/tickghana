<?php
include 'db.php';

// Fetch all events for the filter dropdown
$event_result = $conn->query("SELECT id, event_title FROM events");
$events = [];
if ($event_result->num_rows > 0) {
    while ($row = $event_result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Get selected event from the filter
$selected_event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;

// Prepare the query to fetch ticket purchases
$sql = "SELECT pt.id, pt.full_name, pt.email, pt.reference_number, pt.purchase_date, t.ticket_name, e.event_title, t.ticket_price 
        FROM purchased_tickets pt
        JOIN tickets t ON pt.ticket_id = t.id
        JOIN events e ON t.event_id = e.id";

// Add a condition if a specific event is selected
if ($selected_event_id) {
    $sql .= " WHERE e.id = ?";
}

$sql .= " ORDER BY pt.purchase_date DESC";

$stmt = $conn->prepare($sql);

// Bind the event filter if it's applied
if ($selected_event_id) {
    $stmt->bind_param("i", $selected_event_id);
}

$stmt->execute();
$purchases = $stmt->get_result();

$total_amount = 0; // To store the total amount for all purchases
$event_total = 0; // To store the total for a specific event

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket Purchases</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/manage_ticket.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
   <div class="manage_ticket_all">
<div class="forms">
<h1>View Ticket Purchases</h1>
</div>

<form method="GET" action="view_purchases.php">
    <div class="forms">
    <label for="event_id">Filter by Event:</label>
    <select name="event_id" id="event_id">
        <option value="">-- Select Event --</option>
        <?php foreach ($events as $event): ?>
            <option value="<?php echo $event['id']; ?>" <?php if ($selected_event_id == $event['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($event['event_title']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    </div>
   <div class="forms">
   <button type="submit">Filter</button>
   </div>
</form>

<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Ticket Name</th>
            <th>Event Title</th>
            <th>Reference Number</th>
            <th>Price</th>
            <th>Purchase Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($purchases->num_rows > 0): ?>
            <?php while ($row = $purchases->fetch_assoc()): ?>
                <?php
                    $ticket_price = $row['ticket_price'];
                    $total_amount += $ticket_price; // Calculate total amount across all events
                    if ($selected_event_id) {
                        $event_total += $ticket_price; // Calculate event total if filtered
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['ticket_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                    <td><?php echo number_format($ticket_price, 2); ?></td>
                    <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No purchases found for this event.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h3>
    <?php if ($selected_event_id): ?>
        Total Amount for Selected Event: <?php echo number_format($event_total, 2); ?>
    <?php endif; ?>
</h3>

<h3>
    Total Amount for All Purchases: <?php echo number_format($total_amount, 2); ?>
</h3>
   </div>

</body>
</html>
