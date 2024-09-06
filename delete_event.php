<?php
include 'db.php';

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Delete event from the database
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        header("Location: view_events.php");
        exit();
    } else {
        echo "Error deleting event: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
