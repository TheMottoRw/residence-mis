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
            background-color: #3498db;
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

        /* Updated styles for the text input field */
        input[type="tel"], input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 16px; /* Increased padding for larger input */
            margin: 8px 0;
            border: 2px solid #3498db; /* Increased border thickness */
            border-radius: 10px; /* Added larger border-radius for rounded corners */
            font-size: 16px; /* Increased font size */
            box-sizing: border-box;
            outline: none; /* Remove default outline */
        }

        input[type="tel"]:focus {
            border-color: #2980b9; /* Highlight border color on focus */
            box-shadow: 0 0 8px rgba(41, 128, 185, 0.6); /* Subtle shadow on focus */
        }

        .btn {
            background-color: #3498db;
            color: white;
            padding: 16px;
            border: none;
            border-radius: 10px; /* Added border-radius for the button */
            cursor: pointer;
            display: inline-block;
            margin: 20px 0;
            font-size: 16px;
            width: auto;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .footer {
            background-color: #3498db;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
            width: 2000px;
        }

        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            margin-top: 20px;
            border-radius: 5px;
        }

        /* Style for the rules container */
        .rules-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .rules-container h4 {
            text-align: center;
            color: #3498db;
            text-decoration: underline;
        }

        .rules-container ul {
            list-style-type: none;
            padding-left: 20px;
        }

        .rules-container ul li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Citizen's Residence Management System (CRMS)</h1>
    <p>Hello, Citizen. Please request your residence certificate below.
         For system users, you can click here to <a href="login.php"><font color="black"><h2>Login</h2></a></font></p>
</header>

<div class="container">
    <div class="form-container">
        <h3>Request Residence Certificate</h3>

        <!-- Display any error message -->
        <?php if (isset($error_message)): ?>
            <div class="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form to request the certificate -->
        <form action="certificateRequest.php" method="POST">
            <label for="identifier">Enter Your Resident ID or RegNo</label>
            <!-- Change the input type to "tel" and add pattern for numeric input -->
            <input type="tel" id="identifier" name="identifier" pattern="[0-9]*" inputmode="numeric" required placeholder="Enter your Resident ID or number" title="Only numeric values are allowed">

            <label for="identifier">Select Your Date of birth</label>
            <!-- Change the input type to "tel" and add pattern for numeric input -->
            <input type="date" id="dob" name="dob" required placeholder="Select your date of birth">

            <input type="submit" value="Request Certificate" class="btn">
        </form>
    </div>

    <!-- Rules and Regulations Section -->
    <div class="rules-container">
        <h4><u>Rules and Regulations</u></h4>
        <ul><b>
            <li>- The certificate is valid only if the data is accurate.</li>
            <li>- Ensure to present the certificate for official verification when required.</li>
            <li>- The certificate must be signed by an authorized official to be valid.</li>
            <li>- The Validity of certificate is only one day.</li>
            <li>- If any information is incorrect, it must be reported immediately to the issuing authority.</li></b>
        </ul>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Citizen Residence Management System | All Rights Reserved</p>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
