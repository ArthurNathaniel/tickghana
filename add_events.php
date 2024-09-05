<?php
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_title = $_POST['event_title'];
    $event_msg = $_POST['event_msg'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_price = $_POST['event_price'];
    
    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Upload the file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert event details
        $sql = "INSERT INTO events (image, event_title, event_msg, event_date, event_time, event_price) 
                VALUES ('$image', '$event_title', '$event_msg', '$event_date', '$event_time', '$event_price')";

        if ($conn->query($sql) === TRUE) {
            $event_id = $conn->insert_id; // Get the ID of the newly created event

            // Insert ticket details
            foreach ($_POST['ticket_name'] as $key => $ticket_name) {
                $ticket_price = $_POST['ticket_price'][$key];
                $ticket_sql = "INSERT INTO tickets (event_id, ticket_name, ticket_price) 
                               VALUES ('$event_id', '$ticket_name', '$ticket_price')";
                $conn->query($ticket_sql);
            }

            echo "New event and tickets created successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <script>
        function addRow() {
            const table = document.getElementById('ticket_table');
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="ticket_name[]" required></td>
                <td><input type="number" name="ticket_price[]" required></td>
                <td><button type="button" onclick="deleteRow(this)">Delete</button></td>
            `;
        }

        function deleteRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</head>
<body>

<h1>Add Event</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <label for="event_title">Event Title:</label><br>
    <input type="text" name="event_title" required><br><br>

    <label for="event_msg">Event Description:</label><br>
    <textarea name="event_msg" required></textarea><br><br>

    <label for="event_date">Event Date:</label><br>
    <input type="date" name="event_date" required><br><br>

    <label for="event_time">Event Time:</label><br>
    <input type="time" name="event_time" required><br><br>

    <label for="event_price">Event Price:</label><br>
    <input type="number" name="event_price" required><br><br>

    <label for="image">Event Image:</label><br>
    <input type="file" name="image" required><br><br>

    <h2>Ticket Details</h2>
    <table id="ticket_table" border="1">
        <thead>
            <tr>
                <th>Ticket Name</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" name="ticket_name[]" required></td>
                <td><input type="number" name="ticket_price[]" required></td>
                <td><button type="button" onclick="deleteRow(this)">Delete</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" onclick="addRow()">Add Ticket</button><br><br>

    <input type="submit" value="Add Event">
</form>

</body>
</html>

