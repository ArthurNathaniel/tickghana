<?php
session_start();
require_once 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate reset code
        $reset_code = rand(100000, 999999); // Generate a 6-digit code
        $expiry_time = date("Y-m-d H:i:s", strtotime("+1 hour")); // Code valid for 1 hour

        // Save reset code and expiry time to database
        $sql = "UPDATE users SET reset_code = ?, reset_code_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $reset_code, $expiry_time, $email);
        $stmt->execute();

        // Set success message
        $message = "A password reset code has been sent to your email.";

        // Return response as JSON with the reset code
        echo json_encode(['message' => $message, 'reset_code' => $reset_code]);
    } else {
        $message = "No account found with that email address.";
        echo json_encode(['message' => $message]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
</head>
<body>
<div class="forms_all">
    <div class="forms">
        <div class="logo"></div>
        <h2>Forgot Password</h2>
    </div>

    <div id="message"></div>

    <form id="forgot-password-form">
        <div class="forms">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="forms">
            <button type="submit">Send Reset Code</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        emailjs.init('i3ArGy6b1XhA1vFbr'); // Initialize EmailJS

        document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            fetch('forgot_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('message').innerText = data.message;

                if (data.message.includes("sent to your email")) {
                    emailjs.send('service_d8bh0d4', 'template_zdl189r', {
                        to_email: formData.get('email'),
                        reset_code: data.reset_code // Use the reset code from the response
                    })
                    .then(function(response) {
                        console.log('Success:', response);
                        window.location.href = 'reset_password.php'; // Redirect to reset password page
                    }, function(error) {
                        console.error('EmailJS Failed:', error); // Improved error logging
                    });
                }
            })
            .catch(error => console.error('Fetch Error:', error));
        });
    });
</script>
</body>
</html>