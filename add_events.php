<?php
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_title = $_POST['event_title'];
    $event_msg = $_POST['event_msg'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_price = $_POST['event_price'];
    $event_location = $_POST['event_location'];  // Event Location
    $google_map_link = $_POST['google_map_link'];  // Google Maps Link

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Upload the file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert event details including location and Google Maps link
        $sql = "INSERT INTO events (image, event_title, event_msg, event_date, event_time, event_price, event_location, google_map_link) 
                VALUES ('$image', '$event_title', '$event_msg', '$event_date', '$event_time', '$event_price', '$event_location', '$google_map_link')";

        if ($conn->query($sql) === TRUE) {
            $event_id = $conn->insert_id; // Get the ID of the newly created event

            // Insert ticket details
            foreach ($_POST['ticket_name'] as $key => $ticket_name) {
                $ticket_price = $_POST['ticket_price'][$key];
                $ticket_sql = "INSERT INTO tickets (event_id, ticket_name, ticket_price) 
                               VALUES ('$event_id', '$ticket_name', '$ticket_price')";
                $conn->query($ticket_sql);
            }

            echo "<script>alert('New event and tickets created successfully!'); window.location.href = 'add_events.php';</script>";
        } else {
            echo "Error: " . $sql . "" . $conn->error;
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
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/add_events.css">
    <script>
        function addRow() {
            const table = document.getElementById('ticket_table');
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="ticket_name[]" required></td>
                <td><input type="number" name="ticket_price[]" required></td>
                <td><button class="red" type="button" onclick="deleteRow(this)"><i class="fa-solid fa-trash"></i></button></td>
            `;
        }

        function deleteRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="add_events_all">
<h1>Add Event</h1>
<form action="" method="POST" enctype="multipart/form-data">
  <div class="forms">
  <label for="event_title">Event Title:</label>
  <input type="text" name="event_title" required>
  </div>

  <div class="forms">
    <label for="event_msg">Event Description:</label>
    <div id="editor-container" style="height: 200px;"></div>
    <input type="hidden" name="event_msg" id="event_msg" required>
</div>


   <div class="forms">
   <label for="event_date">Event Date:</label>
   <input type="date" name="event_date" required>
   </div>

  <div class="forms">
  <label for="event_time">Event Time:</label>
  <input type="time" name="event_time" required>
  </div>

   <div class="forms">
   <label for="event_price">Event Price:</label>
   <input type="number" name="event_price" required>
   </div>

  <div class="forms">
  <label for="image">Event Image:</label>
  <input type="file" name="image" required>
  </div>
  <div class="forms">
    <label for="event_location">Event Location:</label>
    <input type="text" name="event_location" required>
</div>

<div class="forms">
    <label for="google_map_link">Google Maps Link:</label>
    <input type="url" name="google_map_link" >
</div>

   <div class="forms">
   <h2>Ticket Details</h2>
   </div>
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
                <td><button class="red"  type="button" onclick="deleteRow(this)"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        </tbody>
    </table>
<div class="forms green">
<button type="button" onclick="addRow()">Add Ticket</button>
</div>
<div class="forms">
    <button type="submit" >Add Event</button>
</div>
    <!-- <input type="submit" value="Add Event"> -->
</form>
</div>
<script>
    // Initialize Quill editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': '1' }, { 'header': '2' }, { 'font': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
                [{ 'align': [] }],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    });

    // Sync Quill content with hidden input field
    quill.on('text-change', function() {
        document.getElementById('event_msg').value = quill.root.innerHTML;
    });

    // Initialize hidden input with any existing value
    document.getElementById('event_msg').value = quill.root.innerHTML;
</script>

</body>
</html>

