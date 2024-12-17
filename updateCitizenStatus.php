<?php include 'header.php'; ?> 
<?php include 'connect.php'; ?>
<?php
// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (!isset($_GET['statusid'])) {
    die("ID not specified.");
}

$id = $_GET['statusid'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT * FROM Status WHERE StatusID = ?");
$stmt->bind_param("s", $id);  // Assuming StatusID is a string (varchar type)
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $message = $_POST['message'];  // Use $_POST for the message
    $description = $_POST['description'];  // Description comes from the form as well
    
    // Prepare and execute update statement
    $stmt = $conn->prepare("UPDATE status SET 
        Message = ?, 
        Description = ? 
        WHERE StatusID = ?");

    // Check if prepare was successful
    if ($stmt === false) {
        die('Error preparing the SQL statement: ' . $conn->error);
    }

    // Bind parameters for update query
    $stmt->bind_param('sss', 
        $message, 
        $description,
        $id  // Bind the StatusID for the WHERE clause
    );

    // Execute the query
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>Record updated successfully</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resident</title>
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
    </style>
</head>
<body>
<div class="container mt-5 form-container">
    <h2 class="text-center">Update Citizen's Status</h2>
    <form action="updatecitizenStatus.php?statusid=<?php echo htmlspecialchars($record['StatusID']); ?>" method="POST">
        <div class="form-group">
            <label for="message">Citizen's Status:</label>
            <input type="text" class="form-control" id="message" name="message" value="<?php echo htmlspecialchars($record['Message']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($record['Description']); ?>" required>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Update</button>
        </div>
    </form>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
