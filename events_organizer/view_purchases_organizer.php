<?php
session_start(); // Start the session

// Check if organizer is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Include database connection
require 'db.php';

// Get the event ID from the session (assigned during login)
$event_id = $_SESSION['event_id'];

// Fetch the event title
$event_title_sql = "SELECT event_title FROM events WHERE id = ?";
$event_title_stmt = $conn->prepare($event_title_sql);
$event_title_stmt->bind_param("i", $event_id);
$event_title_stmt->execute();
$event_title_result = $event_title_stmt->get_result();
$event_title_row = $event_title_result->fetch_assoc();
$event_title = $event_title_row['event_title'];

// Fetch ticket purchases for this event with prices
$sql = "SELECT pt.id, pt.full_name, pt.email, pt.reference_number, pt.purchase_date, t.ticket_name, t.ticket_price 
        FROM purchased_tickets pt
        JOIN tickets t ON pt.ticket_id = t.id
        WHERE t.event_id = ? 
        ORDER BY pt.purchase_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$purchases = $stmt->get_result();

// Calculate total amount gained
$total_amount = 0;
while ($row = $purchases->fetch_assoc()) {
    $total_amount += $row['ticket_price'];
}

// Reset result pointer to fetch rows again
$purchases->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket Purchases</title>
    <link rel="stylesheet" href="../css/base.css">
</head>
<body>
    <h1>Ticket Purchases for Event: <?php echo htmlspecialchars($event_title); ?></h1>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Ticket Name</th>
                <th>Reference Number</th>
                <th>Purchase Date</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($purchases->num_rows > 0): ?>
                <?php while ($row = $purchases->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['ticket_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                        <td><?php echo number_format($row['ticket_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="5"><strong>Total Amount Gained:</strong></td>
                    <td><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="6">No ticket purchases found for this event.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
