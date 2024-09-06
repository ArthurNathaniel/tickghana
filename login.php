<?php
session_start();
require_once 'db.php'; // Ensure this file connects to the database and assigns it to $conn

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Simple validation
    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        // Prepare and execute query to check email and password
        $sql = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $error = "Database query failed.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['logged_in'] = true; // Mark the user as logged in
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
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
    <title>Login</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<section class="login_all">
    <div class="forms_all">
        <form action="" method="post">
            <div class="forms_title forms_logo">
                <!-- Add your logo or title here -->
            </div>
            <div class="forms">
                <h3>Login as an Admin</h3>
            </div>
            <?php if ($error): ?>
                <div class="forms">
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            <div class="forms">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="forms">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form">
            <input type="checkbox" id="showPin"> Show Password
        </div>
            <div class="forms">
                <button type="submit">Login</button>
            </div>
            <div class="forms">
                <p>Forgot password <a href="forgot_password.php">Click here</a></p>
            </div>
        </form>
    </div>

</section>
<script>
        document.getElementById('showPin').addEventListener('change', function() {
            var newPasswordInput = document.getElementById('password');
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
