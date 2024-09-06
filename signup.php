<?php
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            $error = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
            if ($stmt->execute()) {
                $success = "Registration successful!";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<section class="signup_all">
    <div class="forms_all">
        <form action="signup.php" method="post">
            <div class="forms_title forms_logo"></div>
            <div class="forms">
                <h3>Sign Up</h3>
            </div>
            <?php if ($error): ?>
                <div class="forms">
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="forms">
                    <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            <div class="forms">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
            </div>
            <div class="forms">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="forms">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="forms">
                <button type="submit">Sign Up</button>
            </div>
            <div class="forms">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>
</section>
</body>
</html>
