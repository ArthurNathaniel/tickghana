<?php
session_start(); // Start the session

// Include database connection
require 'db.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the organizer details from the database
    $stmt = $conn->prepare("SELECT id, password, event_id FROM organizers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $event_id);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables for the logged-in organizer
            $_SESSION['logged_in'] = true;
            $_SESSION['organizer_id'] = $id;
            $_SESSION['event_id'] = $event_id;

            // Redirect to the ticket purchases page
            header("Location: view_purchases_organizer.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Organizer not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Login</title>
    <link rel="stylesheet" href="./css/base.css">
</head>
<body>
    <h1>Organizer Login</h1>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>

        <?php if ($error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
