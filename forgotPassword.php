<?php
include_once "helper/HelperUtils.php";
include_once "helper/MailUtils.php";
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
$error_message = "";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Validate email
        // Generate a reset token (using a simple random string)
        $token = bin2hex(random_bytes(16));
        $code = generateRandomString(6);
        //check user exists
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user){
            $res = sendRequest(["to"=>$email,"subject"=>"Reset password verification code","body"=>"Hello ".$user['Lastname'].",This is your verification code to reset password: ".$code."<br> Best regards,<br>CRMS"]);
            $res = json_decode($res);
            if($res->status){
                // Insert token into database (assuming you have a 'password_resets' table)
                $stmt = $pdo->prepare("UPDATE users SET verification_code=:code,need_verification=:need WHERE Email=:email");
                $stmt->execute(['code' => $code, 'need' => '1', 'email' => $email]);
                $_SESSION['email'] = $email;
                $_SESSION['type'] = "user";
                echo "<script>alert('Check your email for verification code');window.location='verifyCode.php';</script>";
            }else{
                echo "<div class='alert alert-danger'>".$res->message."</div>";
            }
        }else {
            $query = "SELECT * FROM resident WHERE Telephone =:email";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $res = sendRequest(["to" => $email."@yopmail.com", "subject" => "Reset password verification code", "body" => "Hello " . $user['Lastname'] . ",This is your verification code to reset password: <b>" . $code . "</b><br> Best regards,<br>CRMS"]);
                $res = json_decode($res);
                if ($res->status) {
                    // Insert token into database (assuming you have a 'password_resets' table)
                    $stmt = $pdo->prepare("UPDATE resident SET verification_code=:code,need_verification=:need WHERE Telephone=:email");
                    $stmt->execute(['code' => $code, 'need' => '1', 'email' => $email]);
                    $_SESSION['email'] = $email;
                    $_SESSION['type'] = "resident";
                    echo "<script>alert('Check your email for verification code');window.location='verifyCode.php';</script>";
                } else {
                    $error_message =  "<div class='alert alert-danger'>" . $res->message . "</div>";
                }
            } else {
                $error_message =  "<div class='alert alert-danger'>User with that email not found</div>";
            }
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
            <?php
            if($error_message!="") echo $error_message;
            ?>
            <div class="form-group">
                <label for="email">Enter your email</label>
                <input type="text" class="form-control" id="email" name="email" required>
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
