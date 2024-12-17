<?php   
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Composer's autoload.php
require 'vendor/autoload.php';

// Import PHPMailer classes here, at the top of the file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$host = 'localhost';
$dbname = 'crms';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate a reset token (using a simple random string)
        $token = bin2hex(random_bytes(16));

        // Insert token into database (assuming you have a 'password_resets' table)
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())");
        $stmt->execute(['email' => $email, 'token' => $token]);

        // PHPMailer logic to send the reset link email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to Gmail's
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';  // Your Gmail address
            $mail->Password = 'your-email-password';  // Your Gmail password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable STARTTLS
            $mail->Port = 587;  // Use port 587 for TLS

            // Recipients
            $mail->setFrom('your-email@gmail.com', 'Mailer');
            $mail->addAddress($email);  // Send to the user's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = 'Click the link below to reset your password:<br><a href="http://yourdomain.com/resetPassword.php?token=' . $token . '">Reset Password</a>';

            // Send email
            $mail->send();
            echo "<script>alert('A password reset link has been sent to your email.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Error sending the email: " . $mail->ErrorInfo . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid email address.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CRMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 36px;
        }
        .header p {
            margin-top: 10px;
            font-size: 18px;
        }
        .container {
            margin-top: 50px;
        }
        .alert-box {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Citizen's Residence Management System (CRMS)</h1>
        <p>Hello, Citizen. Please request your residence certificate below.<br>For system users, you can click <a href="login.php" class="text-light"><strong><h2>Login</h2></strong></a> here.</p>
    </div>
    <!-- Main Content -->
    <div class="container">
        <h3>Forgot Password</h3>
        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>

    <!-- Optional Alert Box for Success/Error -->
    <div class="alert-box">
        <!-- Optional success/error alert messages will appear here if needed -->
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
