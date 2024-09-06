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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Change password
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match.";
    } else {
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $hashedPassword = $stmt->get_result()->fetch_assoc()['password'];
        $stmt->close();

        if (password_verify($currentPassword, $hashedPassword)) {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newHashedPassword, $userId);
            if ($stmt->execute()) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/settings.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<section class="settings_all">
    <div class="settings_container">
        <h2>Change Password</h2>
        
        <form action="change_password.php" method="post">
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
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="forms">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="forms">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form">
            <input type="checkbox" id="showPin"> Show Password
        </div>
            <div class="forms">
                <button type="submit" name="change_password">Change Password</button>
            </div>
        </form>
    </div>
</section>
<script>
        document.getElementById('showPin').addEventListener('change', function() {
            var newPasswordInput = document.getElementById('new_password');
            var confirmPasswordInput = document.getElementById('confirm_password');
            if (this.checked) {
                newPasswordInput.type = 'text';
                confirmPasswordInput.type = 'text';
            } else {
                newPasswordInput.type = 'password';
                confirmPasswordInput.type = 'password';
            }
        });
    </script>
</body>
</html>
