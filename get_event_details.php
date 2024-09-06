<?php
include 'db.php';

$event_id = $_GET['id'];

// Fetch event details
$sql = "SELECT event_title, event_msg, event_date, event_time, event_price FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Fetch ticket details
$sql_tickets = "SELECT ticket_name, ticket_price FROM tickets WHERE event_id = ?";
$stmt_tickets = $conn->prepare($sql_tickets);
$stmt_tickets->bind_param('i', $event_id);
$stmt_tickets->execute();
$result_tickets = $stmt_tickets->get_result();

$tickets = [];
while ($ticket = $result_tickets->fetch_assoc()) {
    $tickets[] = $ticket;
}

echo json_encode([
    'event_title' => $event['event_title'],
    'event_msg' => $event['event_msg'],
    'event_date' => $event['event_date'],
    'event_time' => $event['event_time'],
    'event_price' => $event['event_price'],
    'tickets' => $tickets
]);

$conn->close();
?>
