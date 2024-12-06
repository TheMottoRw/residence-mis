<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Resident</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    </head>
</head>

<body background="img src="image1.jpg" class="d-block w-100" alt="...">
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
if (!isset($_GET['identifier'])) {
    die("ID not specified.");
}

$id = $_GET['identifier'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT * FROM resident WHERE Identifier = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_GET['identifier'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $telephone = $_POST['telephone'];
    $gender = $_POST['gender'];
    $id = $_POST['id'];
    $mothernames = $_POST['mothernames'];
    $fathernames = $_POST['fathernames'];

    // Prepare the update statement (fixed the comma issue)
    $stmt = $conn->prepare("UPDATE Resident SET 
        Firstname = ?, 
        Lastname = ?, 
        DoB = ?, 
        Telephone = ?, 
        Gender = ?, 
        ID = ?, 
        FatherNames = ?, 
        MotherNames = ? 
        WHERE Identifier = ?");
    
    if ($stmt === false) {
        die("Error preparing the SQL statement: " . $conn->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param('sssisissi', 
        $firstname,
        $lastname,
        $dob,
        $telephone,
        $gender,
        $id,
        $fathernames,
        $mothernames,
        $identifier
    );

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>Record updated successfully</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch available statuses from the Message table
$statusOptions = [];
$sql = "SELECT StatusID, Message FROM status"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusOptions[] = $row; // Store StatusID and Message in an array
    }
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
<h2 class="text-center">Update Citizen's info</h2>
    <form action="updateCitizen.php?identifier=<?php echo htmlspecialchars($record['Identifier']); ?>" method="POST">
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($record['Firstname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($record['Lastname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($record['DoB']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($record['Telephone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php echo $record['Gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $record['Gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($record['ID']); ?>" required>
        </div>
        <div class="form-group">
            <label for="district">MotherNames:</label>
            <input type="text" class="form-control" id="mothernames" name="mothernames" value="<?php echo htmlspecialchars($record['MotherNames']); ?>" required>
        </div>
        <div class="form-group">
            <label for="district">FatherNames:</label>
            <input type="text" class="form-control" id="fathernames" name="fathernames" value="<?php echo htmlspecialchars($record['FatherNames']); ?>" required>
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
