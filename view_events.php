<?php
include 'db.php';

// Fetch all events from the database
$events = [];
$sql = "SELECT id, event_title, event_msg, event_date, event_time, event_price, event_location, google_map_link, image FROM events ORDER BY event_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/view_events.css">
    <script>
        // Show modal and fill event data
        function showModal(eventId) {
            fetch('get_event_details.php?id=' + eventId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal_title').innerText = data.event_title;
                    document.getElementById('modal_date').innerText = data.event_date;
                    document.getElementById('modal_time').innerText = data.event_time;
                    document.getElementById('modal_price').innerText = data.event_price;
                    document.getElementById('modal_msg').innerHTML = data.event_msg;
                    document.getElementById('modal_location').innerText = data.event_location;
                    document.getElementById('modal_map_link').href = data.google_map_link;
                    document.getElementById('modal_image').src = 'uploads/' + data.image;

                    let ticketTable = document.getElementById('modal_tickets');
                    ticketTable.innerHTML = '';
                    data.tickets.forEach(ticket => {
                        let row = `<tr><td>${ticket.ticket_name}</td><td>${ticket.ticket_price}</td></tr>`;
                        ticketTable.innerHTML += row;
                    });

                    document.getElementById('modal').style.display = 'block';
                });
        }

        // Hide modal
        function hideModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Redirect to edit page
        function redirectToEdit(eventId) {
            window.location.href = 'edit_event.php?id=' + eventId;
        }

        // Delete event
        function deleteEvent(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                window.location.href = "delete_event.php?id=" + eventId;
            }
        }
    </script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="events_all">
    <h1>Events</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Event Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['event_title']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td>
                            <button type="button" onclick="showModal('<?php echo $event['id']; ?>')">View</button>
                            <button type="button" onclick="redirectToEdit('<?php echo $event['id']; ?>')">Edit</button>
                            <button type="button" onclick="deleteEvent('<?php echo $event['id']; ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No events found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal to show event details -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="hideModal()">&times;</span>
        <h2 id="modal_title"></h2>
        <p><strong>Date:</strong> <span id="modal_date"></span></p>
        <p><strong>Time:</strong> <span id="modal_time"></span></p>
        <p><strong>Price:</strong> <span id="modal_price"></span></p>
        <p><strong>Description:</strong> <div id="modal_msg"></div></p>
        <p><strong>Location:</strong> <span id="modal_location"></span></p>
        <p><strong>Google Maps Link:</strong> <a id="modal_map_link" href="#" target="_blank">View on Google Maps</a></p>
        <img id="modal_image" src="" alt="Event Image" style="width:100%; max-width:600px;">
        <h3>Tickets</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Ticket Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="modal_tickets"></tbody>
        </table>
    </div>
</div>

<style>
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: red;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
</body>
</html>
