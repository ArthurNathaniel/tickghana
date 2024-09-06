<?php
include 'db.php';

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);

    $sql = "SELECT id, event_title, event_date, event_time, event_price, event_msg, event_location, google_map_link, image FROM events WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Fetch tickets related to the event
    $tickets = [];
    $sql = "SELECT ticket_name, ticket_price FROM tickets WHERE event_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    echo json_encode([
        'id' => $event['id'],
        'event_title' => $event['event_title'],
        'event_date' => $event['event_date'],
        'event_time' => $event['event_time'],
        'event_price' => $event['event_price'],
        'event_msg' => $event['event_msg'],
        'event_location' => $event['event_location'],
        'google_map_link' => $event['google_map_link'],
        'image' => $event['image'],
        'tickets' => $tickets
    ]);

    $stmt->close();
    $conn->close();
}
?>
