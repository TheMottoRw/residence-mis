<?php
session_start();

// Include database connection (if needed for fetching user data)
include 'connect.php';

// Check if the user is logged in (optional)
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = "Guest";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Residence Management System</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            margin: 20px 0;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }

        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            margin-top: 20px;
            border-radius: 5px;
        }

    </style>
</head>
<body>

<header>
    <h1>Welcome to the Citizen Residence Management System</h1>
    <p>Hello, <?php echo htmlspecialchars($username); ?>. Please request your residence certificate below.</p>
</header>

<div class="container">
    <div class="form-container">
        <h3>Request Residence Certificate</h3>

        <!-- Display any error message -->
        <?php if (isset($error_message)): ?>
            <div class="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form to request the certificate -->
        <form action="certificaterequest.php" method="POST">
            <label for="identifier">Enter Your Resident ID or Number</label>
            <input type="text" id="identifier" name="identifier" required placeholder="Enter your Resident ID or number">

            <input type="submit" value="Request Certificate" class="btn">
        </form>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Citizen Residence Management System | All Rights Reserved</p>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
