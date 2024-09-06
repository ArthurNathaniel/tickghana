<?php
session_start(); // Start the session

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Include database connection
require 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $event_id = $_POST['event_id'];

    // Insert the new organizer into the database
    $stmt = $conn->prepare("INSERT INTO organizers (username, password, event_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $event_id);

    if ($stmt->execute()) {
        echo "Event organizer registered successfully!";
    } else {
        echo "Error registering organizer: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch all events for dropdown
$event_result = $conn->query("SELECT id, event_title FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Event Organizer</title>
    <link rel="stylesheet" href="./css/base.css">
</head>
<body>
    <h1>Register Event Organizer</h1>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="event_id">Assign Event:</label>
        <select name="event_id" id="event_id" required>
            <option value="">-- Select Event --</option>
            <?php while ($row = $event_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['event_title']); ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Register Organizer</button>
    </form>
</body>
</html>
