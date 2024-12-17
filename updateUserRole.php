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
if (!isset($_GET['roleid'])) {
    die("ID not specified.");
}

$id = $_GET['roleid'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT * FROM Roles WHERE RoleID = ?");
$stmt->bind_param("s", $id);  // Assuming RoleID is a string (varchar type)
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $rolename = $_POST['rolename'];  // Accessing the 'rolename' form field instead of 'RoleName'
    
    // Prepare and execute update statement
    $stmt = $conn->prepare("UPDATE Roles SET 
        RoleName = ? 
        WHERE RoleID = ?");

    // Check if prepare was successful
    if ($stmt === false) {
        die('Error preparing the SQL statement: ' . $conn->error);
    }

    // Bind parameters for update query
    $stmt->bind_param('ss', $rolename, $id);  // Bind RoleName as string and RoleID as string

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
    <title>Edit User Role</title>
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
    <h2 class="text-center">Update a User Role</h2>
    <form action="updateUserRole.php?roleid=<?php echo htmlspecialchars($record['RoleID']); ?>" method="POST">
        <div class="form-group">
            <label for="rolename">User's Role:</label>
            <input type="text" class="form-control" id="rolename" name="rolename"  pattern="[A-Za-z ()]*" inputmode="alphabetic" title="Only alphabetic characters are allowed" value="<?php echo htmlspecialchars($record['RoleName']); ?>" required>
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
