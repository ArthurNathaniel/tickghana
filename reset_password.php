<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reset_code = $_POST['reset_code'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Verify reset code
        $sql = "SELECT id, reset_code_expiry FROM users WHERE reset_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $reset_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $expiry_time = new DateTime($row['reset_code_expiry']);
            $current_time = new DateTime();

            if ($current_time <= $expiry_time) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ?, reset_code = NULL, reset_code_expiry = NULL WHERE reset_code = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $hashed_password, $reset_code);
                $stmt->execute();

                $success = "Password has been reset successfully.";
                
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $error = "Reset code has expired.";
            }
        } else {
            $error = "Invalid reset code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="forms_all">
    <div class="forms">
        <div class="logo"></div>
        <h2>Reset Password</h2>
    </div>

    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>

    <form action="" method="POST">
        <div class="forms">
            <label for="reset_code">Reset Code</label>
            <input type="text" id="reset_code" name="reset_code" required>
        </div>
        <div class="forms">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="forms">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form">
            <input type="checkbox" id="showPin"> Show Password
        </div>
        <div class="forms">
            <button type="submit">Reset Password</button>
        </div>
    </form>

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
</div>
</body>
</html>