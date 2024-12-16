<?php
// Start the session at the top, before any HTML output
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'localhost'; // Your database host
$dbname = 'crms'; // Your database name
$user = 'super'; // Your database username
$pass = ''; // Your database password

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Sanitize inputs
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = md5(htmlspecialchars($password));

    // Validate input
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit();
    }

    // Prepare and execute query to fetch user data
    $query = "SELECT * FROM users WHERE email = :email AND password = :password and Status=1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email, 'password' => $password]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if ($user) {
        // Successful login
        $_SESSION['email'] = $user['Email'];
        $_SESSION['password'] = $user['Password'];
        $_SESSION['role'] = $user['Role'];
        $_SESSION['cell'] = $user['Cell'];
        $_SESSION['village'] = $user['Village'];
        $_SESSION['ID'] = $user['ID'];

        if($user['Role']=='Admin'){
//            header("location:Dashboard.php");
            echo "<script type='text/javascript'> document.location = 'Dashboard.php'; </script>";
        }else{
            echo "<script type='text/javascript'> document.location = 'CertificateRequestsView.php'; </script>";
        }

        exit();
    } else {
        // Prepare and execute query to fetch user data
        $query = "SELECT * FROM resident WHERE Telephone = :email AND password = :password and Citizen_Category='Landlord'";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $email, 'password' => $password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user){
            $_SESSION['email'] = $user['Telephone'];
            $_SESSION['password'] = $user['Password'];
            $_SESSION['role'] = "Landlord";
            $_SESSION['cell'] = $user['Cell'];
            $_SESSION['village'] = $user['Village'];
            $_SESSION['ID'] = $user['ID'];
            echo "<script type='text/javascript'> document.location = 'CertificateRequestsView.php'; </script>";
        }else{
            echo "<script>alert('Invalid email or password, Your Account is not Active..');</script>";
        }
        // Invalid login
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <style>
        body {
            background-image: url('images/image2.jpg');
            background-size: cover;
            background-position: center center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        h3 {
            color: #fff;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 25px;
            border: 1px solid #ccc;
            padding: 12px;
            font-size: 1.1rem;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .btn {
            border-radius: 25px;
            font-weight: bold;
            padding: 12px 30px;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .footer {
            text-align: center;
            padding: 20px 0;
            color: white;
            font-size: 0.9rem;
        }

        .text-muted {
            color: #777;
        }

        .bg-login-image {
            background: url('images/login-bg.jpg') no-repeat center center;
            background-size: cover;
            height: 100%;
            display: none;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: 0.9rem;
        }

        .row {
            margin: 0;
        }

        /* Gradient Overlay for the login box */
        .card-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            z-index: -1;
        }

        /* Header styling */
        .header {
            background-color: #00aaff;
            padding: 20px 0;
            text-align: center;
        }

        .header h3 {
            color: white;
            font-weight: bold;
        }

        .header h3:hover {
            color: #ffdd57;
        }
    </style>
    <title>CRMS - Login</title>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h3>Citizen's Residence Management System (CRMS)</h3>
    </div>

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-8">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <h1 class="h4 text-gray-900">Login to CRMS</h1>
                                    </div>
                                    <form action="#" method="POST">
                                        <div class="form-group">
                                            <label for="email">Email/Phone</label>
                                            <input type="text" class="form-control" id="email" name="email" required>
                                            <small id="emailHelp" class="form-text text-muted">Never share your credentials with anyone else.</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="loginbtn">Login</button>
                                        <button type="reset" class="btn btn-secondary">Clear</button>
                                        <div class="text-center mt-3">
                                            <a class="small" href="Index.php">Homepage</a> || 
                                            <a class="small" href="forgotPassword.php">Forgot Password?</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 CRMS. All rights reserved.</p>
    </div>

    <script src="bootstrap/jquery.slim.js"></script>
    <script src="bootstrap/bootstrap.bundle.js"></script>
</body>

</html>
