<?php
session_start();
require_once 'db.php';

$error = "";
$success = "";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    if (empty($fullName) || empty($email)) {
        $error = "All fields are required.";
    } else {
        $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $fullName, $email, $userId);
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/settings.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<section class="settings_all">
    <div class="settings_container">
        <h2>Update Profile</h2>
        
        <form action="update_profile.php" method="post">
            <?php if ($error): ?>
                <div class="form_error">
                    <p style="color: red"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="form_success">
                    <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="forms">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="forms">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="forms">
                <button type="submit" name="update_profile">Update Profile</button>
            </div>
        </form>
    </div>
</section>
</body>
</html>
