<?php
include 'db.php';

// Initialize variables
$event = [];
$update_error = "";
$success_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    if ($stmt) {
        // Correct bind_param with appropriate data types
        $stmt->bind_param(
            'ssssssssi', 
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
            $success_message = "Event updated successfully.";
        } else {
            $update_error = "Error updating event: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $update_error = "Failed to prepare the SQL statement.";
    }

    $conn->close();
} else {
    // Fetch event details for editing
    $event_id = $_GET['id'] ?? 0;

    $sql = "SELECT id, event_title, event_msg, event_date, event_time, event_price, event_location, google_map_link, image FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/edit_event.css">
    <!-- QuillJS CDN -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="edit_event">
    <h1>Edit Event</h1>
    <?php if ($success_message): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
        <script>
            // Redirect after successful update
            setTimeout(function() {
                window.location.href = 'view_events.php'; // Change to your events list page
            }, 500);
        </script>
    <?php endif; ?>
    <?php if ($update_error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($update_error); ?></p>
    <?php endif; ?>
    <form action="edit_event.php" method="post">
        <input type="hidden" name="edit_event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
        <p>
            <label for="edit_event_title">Event Title:</label>
            <input type="text" id="edit_event_title" name="edit_event_title" value="<?php echo htmlspecialchars($event['event_title']); ?>" required>
        </p>
        <p>
            <label for="edit_event_date">Event Date:</label>
            <input type="date" id="edit_event_date" name="edit_event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
        </p>
        <p>
            <label for="edit_event_time">Event Time:</label>
            <input type="time" id="edit_event_time" name="edit_event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
        </p>
        <p>
            <label for="edit_event_price">Event Price:</label>
            <input type="text" id="edit_event_price" name="edit_event_price" value="<?php echo htmlspecialchars($event['event_price']); ?>" required>
        </p>
        <p>
            <label for="edit_event_msg">Description:</label>
            <div id="quill-editor" style="height: 200px;"><?php echo $event['event_msg']; ?></div>
            <textarea id="edit_event_msg" name="edit_event_msg" style="display: none;"></textarea>
        </p>
        <p>
            <label for="edit_event_location">Location:</label>
            <input type="text" id="edit_event_location" name="edit_event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>">
        </p>
        <p>
            <label for="edit_google_map_link">Google Maps Link:</label>
            <input type="url" id="edit_google_map_link" name="edit_google_map_link" value="<?php echo htmlspecialchars($event['google_map_link']); ?>">
        </p>
        <p>
            <label for="edit_image">Event Image URL:</label>
            <input type="text" id="edit_image" name="edit_image" value="<?php echo htmlspecialchars($event['image']); ?>">
        </p>
        <button type="submit">Update Event</button>
    </form>
</div>

<script>
    // Initialize Quill editor
    var quill = new Quill('#quill-editor', {
        theme: 'snow'
    });

    // Update the hidden textarea with Quill editor content on form submit
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelector('#edit_event_msg').value = quill.root.innerHTML;
    });
</script>
</body>
</html>
