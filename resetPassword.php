<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password']) && isset($_GET['token'])) {
    $password = $_POST['password'];
    $token = $_GET['token'];

    // Hash the new password
    $hashedPassword = md5(htmlspecialchars($password));

    // Check if the token is valid
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND created_at > NOW() - INTERVAL 1 HOUR");
    $stmt->execute(['token' => $token]);
    $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resetRequest) {
        // Reset the password
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->execute(['password' => $hashedPassword, 'email' => $resetRequest['email']]);

        // Delete the reset token (optional)
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);

        echo "<script>alert('Password has been reset successfully. You can now log in.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired token.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CRMS</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
</head>
<body>
    <div class="container">
        <h3>Reset Your Password</h3>

        <?php
        // Check if the token is provided
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            echo "<form method='POST'>
                    <div class='form-group'>
                        <label for='password'>Enter your new password</label>
                        <input type='password' class='form-control' id='password' name='password' required>
                    </div>
                    <button type='submit' class='btn btn-primary'>Submit</button>
                  </form>";
        } else {
            echo "<p>Invalid reset link. Please try again.</p>";
        }
        ?>
    </div>
</body>
</html>
