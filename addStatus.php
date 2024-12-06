<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert User</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .btn-custom-clear {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>

<?php include 'connect.php'; ?>
<?php include 'header.php'; ?>

<?php
// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $message = $_POST['message'];
    $description = $_POST['description'];

    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO status (Message, Description) 
                            VALUES (?, ?)");
    $stmt->bind_param("ss", $message, $description);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>New status added successfully</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>
<div class="form-container">
<div class="container mt-5 form-container">
    <h2 class="text-center">Insert a citizen's Status</h2>
    <form action="addStatus.php" method="POST">
        <div class="form-group">
            <label for="firstname">Citizen Status:</label>
            <input type="text" class="form-control" id="message" name="message" required>
        </div>
        <div class="form-group">
            <label for="lastname">Description:</label>
            <input type="text" class="form-control" id="description" name="description" required>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Submit</button>
            <button type="reset" class="btn btn-custom-clear">Clear</button>
        </div>
    </form>
</div>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
