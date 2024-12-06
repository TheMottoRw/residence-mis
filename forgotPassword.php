<?php
    //<form action="forgot-process.php" method="post">
        //<label for="email">Enter your email:</label>
        //<input type="email" name="email" required>
       // <button type="submit">Submit</button>
    //</form>

    if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generate a random token

    // Save the token and email in the database
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
    $stmt->bind_param('ss', $email, $token);
    $stmt->execute();

    // Send the reset link via email
    $resetLink = "http://yourdomain.com/reset-password.php?token=$token";
    $subject = "Password Reset Request";
    $message = "Click on this link to reset your password: $resetLink";
    $headers = "From: no-reply@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "Password reset link has been sent to your email.";
    } else {
        echo "Failed to send email.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body>
    <form action="reset-process.php" method="GET">
        <input type="hidden" name="token" value=" <?php echo $_GET['token']; ?> ">
        <label for="password">New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>