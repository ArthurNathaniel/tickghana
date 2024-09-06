<?php
include 'db.php';

// Get form data
$event_id = $_POST['edit_event_id'];
$event_title = $_POST['edit_event_title'];
$event_date = $_POST['edit_event_date'];
$event_time = $_POST['edit_event_time'];
$event_price = $_POST['edit_event_price'];
$event_msg = $_POST['edit_event_msg'];
$event_location = $_POST['edit_event_location'];
$google_map_link = $_POST['edit_google_map_link'];
$image = $_POST['edit_image'];

// Update event in the database
$sql = "UPDATE events SET 
            event_title = ?, 
            event_date = ?, 
            event_time = ?, 
            event_price = ?, 
            event_msg = ?, 
            event_location = ?, 
            google_map_link = ?, 
            image = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'sssssssi', 
    $event_title, 
    $event_date, 
    $event_time, 
    $event_price, 
    $event_msg, 
    $event_location, 
    $google_map_link, 
    $image, 
    $event_id
);

if ($stmt->execute()) {
    echo "Event updated successfully.";
} else {
    echo "Error updating event: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
