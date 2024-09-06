<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Include database connection
require 'db.php';

// Fetch user details
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($fullName, $email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/dashboard.css">
</head>
<body>
<?php include 'sidebar.php'; ?>


<style>

</style>



<section class="dashboard_all">
    <div class="dashboard_content">
        <div class="welcome_message">
            <h2>Welcome, <?php echo htmlspecialchars($fullName); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
        </div>
        
        <div class="dashboard_actions">
            <h3>Recent Activity</h3>
            <!-- You can add more details or activity logs here -->

            <div class="actions">
                <a href="settings.php" class="btn">Account Settings</a>
                <a href="logout.php" class="btn">Logout</a>
            </div>
        </div>
    </div>
</section>
</body>
</html>
