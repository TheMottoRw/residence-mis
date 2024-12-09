<?php 
/*
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'localhost'; // Your database host
$dbname = 'crms'; // Your database name
$user = 'root'; // Your database username
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
    $password = htmlspecialchars($password);

    // Validate input
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit();
    }

    // MD5 hash the password entered by the user
    $hashedPassword = md5($password);  // Hash the entered password using MD5

    // Prepare and execute query to fetch user data
    $query = "SELECT email, password FROM Users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify the MD5 password
    if ($user && $user['password'] === $hashedPassword) {
        // Successful login
        $_SESSION['email'] = $user['email'];  // Store the user's email in session
        echo "<script type='text/javascript'> document.location = 'Dashboard.php'; </script>";
        exit();
    } else {
        // Invalid login
        echo "<script>alert('Invalid email or password.');</script>";
    }
} */
?> 
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if($_SESSION['login']!='')
{
$_SESSION['login']='';
}?>

<!---------------------------------------------------------------------------------------------->
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
        }
        h3 {
            color: black;
        }
    </style>
    <title>CRMS - Login</title>
    <!-- header -->
    <div class="row" style="background:skyblue; height:50px;">
        <div class="container" style="text-align:center;">
            <h3>Citizen's Residence Management System (CRMS)</h3>
        </div>
    </div>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-4 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-8">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login to CRMS</h1>
                                    </div>
                                    <form action="#" method="POST">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <small id="emailHelp" class="form-text text-muted">Never share your credentials with anyone else.</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <br>
                                            <button type="submit" class="btn btn-primary" name="login">Submit</button>
                                            <button type="reset" class="btn btn-secondary">Clear</button>
                                        </div>
                                        <div class="text-center">
                                        <a class="small" href="Index.php">Homepage &nbsp</a>||
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
<?php
if(isset($_POST['login']))
{
$email=$_POST['emailid'];
$password=md5($_POST['password']);
$sql ="SELECT Email,Password,UserID FROM users WHERE Email=:email and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

if($query->rowCount() > 0)
{
 foreach ($results as $result)
    {
     $_SESSION['userID']=$result->UserID;
if($result->Status==1)
{
    $_SESSION['login']=$_POST['email'];
    echo "<script type='text/javascript'> document.location ='Dashboard.php'; </script>";
} 
else 
{
    echo "<font color='Red'>Your Account Has been blocked, Please contact admin!</font>";
    }
}

} 
else
{
// Invalid login
echo "<script>alert('Invalid email or password.');</script>";
}
}
?>
 <script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
