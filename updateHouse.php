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
if (!isset($_GET['houseno'])) {
    die("ID not specified.");
}

$id = $_GET['houseno'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT * FROM houses WHERE HouseNo = ?");
$stmt->bind_param("s", $id);  // Assuming HouseNo is a string (varchar type)
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $houseno = $_GET['houseno'];
    $ownerid = $_POST['ownerid'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $sector = $_POST['sector'];
    $cell = $_POST['cell'];
    $village = $_POST['village'];
    $status = $_POST['status'];

    // Prepare and execute update statement
    $stmt = $conn->prepare("UPDATE houses SET 
        ID = ?, 
        Province = ?, 
        District = ?, 
        Sector = ?, 
        Cell = ?, 
        Village = ?,
        Status= ?
        WHERE HouseNo = ?");

    // Check if prepare was successful
    if ($stmt === false) {
        die('Error preparing the SQL statement: ' . $conn->error);
    }

    // Bind parameters for update query
    $stmt->bind_param('isssssss', 
        $ownerid, 
        $province, 
        $district, 
        $sector, 
        $cell, 
        $village, 
        $status,
        $houseno
    );

    // Execute the query
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>Record updated successfully</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
// Fetch available ID from the Residents ID table
$owneridOptions = [];
$sql = "SELECT ID FROM resident"; // Query the Resident table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $owneridOptions[] = $row; // Store OwnerID and Message in an array
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
<h2 class="text-center">Update Resident's info</h2>
    <form action="updateHouse.php?houseno=<?php echo htmlspecialchars($record['HouseNo']); ?>" method="POST">
    <div class="form-group">
            <label for="ownerid">House Owner:</label>
            <select class="form-control" id="ownerid" name="ownerid" required>
            <option value=""><?php echo htmlspecialchars($record['ID']); ?></option>
                <?php 
                    // Dynamically populate the status dropdown
                    foreach ($owneridOptions as $ownerid) {
                        echo "<option value='" . $ownerid['ID'] . "'>" . $ownerid['ID'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="province">Province:</label>
            <input type="text" class="form-control" id="province" name="province" value="<?php echo htmlspecialchars($record['Province']); ?>" required>
        </div>
        <div class="form-group">
            <label for="district">District:</label>
            <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($record['District']); ?>" required>
        </div>
        <div class="form-group">
            <label for="sector">Sector:</label>
            <input type="text" class="form-control" id="sector" name="sector" value="<?php echo htmlspecialchars($record['Sector']); ?>" required>
        </div>
        <div class="form-group">
            <label for="cell">Cell:</label>
            <input type="text" class="form-control" id="cell" name="cell" value="<?php echo htmlspecialchars($record['Cell']); ?>" required>
        </div>
        <div class="form-group">
            <label for="village">Village:</label>
            <input type="text" class="form-control" id="village" name="village" value="<?php echo htmlspecialchars($record['Village']); ?>" required>
        </div>
        <div class="form-group">
            <label for="village">Status:</label>
            <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($record['Status']); ?>" required>
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
