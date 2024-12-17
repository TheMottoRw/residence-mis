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
$user = 'super';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    if($password!=$confirmPassword){
    echo "<script>alert('Passwords do not match!');</script>";
    }else{
        $userQuery = "UPDATE users SET password=:pwd,need_verification=:need WHERE Email=:email AND verification_code=:code";
        $residentQuery = "UPDATE resident SET password=:pwd,need_verification=:need WHERE Telephone=:email AND verification_code=:code";
        if($_SESSION['type']=='user'){
            $stmt = $pdo->prepare($userQuery);
        }else{
            $stmt = $pdo->prepare($residentQuery);
        }
        $stmt->execute(['pwd'=>md5($password), 'need' => '0', 'email' => $_SESSION['email'],'code' => $_SESSION['code']]);
        echo "<script>alert('Password reset successfully!');window.location='login.php</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify code - CRMS</title>
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
    <h3>Change password</h3>
    <form method="POST">
        <div class="form-group">
            <label for="password">New password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
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
