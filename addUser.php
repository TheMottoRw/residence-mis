<?php include 'header.php'; ?> 
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
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $id = $_POST['id'];
    $telephone = $_POST['telephone'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $sector = $_POST['sector'];
    $cell = $_POST['cell'];
    $village = $_POST['village'];
    $status = $_POST['status'];

    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO users (Firstname, Lastname, Email, Password, Role, ID, Telephone, Province, District, Sector, Cell, Village, Status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $firstname, $lastname, $email, $password, $role, $id, $telephone, $province, $district, $sector, $cell, $village, $status);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>New user created successfully</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch available statuses from the Status table
$statusOptions = [];
$sql = "SELECT Message FROM status"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusOptions[] = $row; // Store Status and Message in an array
    }
}

// Fetch available Provinces
$provinceOptions = [];
$sql = "SELECT Province FROM provinces"; // Query the province table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $provinceOptions[] = $row; // Store provinces in an array
    }
}

// Fetch available districts
$districtOptions = [];
$sql = "SELECT District FROM districts"; // Query the districts table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $districtOptions[] = $row; // Store districts in an array
    }
}

// Fetch available sectors
$sectorOptions = [];
$sql = "SELECT Sector FROM sectors"; // Query the sectors table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sectorOptions[] = $row; // Store sectors in an array
    }
}

// Fetch available cells
$cellOptions = [];
$sql = "SELECT Cell FROM cells"; // Query the cells table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cellOptions[] = $row; // Store cells in an array
    }
}

// Fetch available villages
$villageOptions = [];
$sql = "SELECT Village FROM villages"; // Query the villages table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $villageOptions[] = $row; // Store villages in an array
    }
}
$roleOptions = [];
$sql = "SELECT RoleName FROM roles"; // Query the Roles table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $roleOptions[] = $row; // Store Roles in an array
    }
}
?>

<div class="container mt-5 form-container">
    <h2 class="text-center">Insert New User</h2>
    <form action="addUser.php" method="POST">
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Default-Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="">Select User Role</option>
                <?php 
                    // Dynamically populate the role dropdown
                    foreach ($roleOptions as $role) {
                        echo "<option value='" . $role['RoleName'] . "'>" . $role['RoleName'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id" required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" name="telephone" required>
        </div>
        <div class="form-group">
            <label for="province">Province:</label>
            <select class="form-control" id="province" name="province" required>
                <option value="">Select Province</option>
                <?php 
                    // Dynamically populate the province dropdown
                    foreach ($provinceOptions as $province) {
                        echo "<option value='" . $province['Province'] . "'>" . $province['Province'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="district">District:</label>
            <select class="form-control" id="district" name="district" required>
                <option value="">Select District</option>
                <?php 
                    // Dynamically populate the district dropdown
                    foreach ($districtOptions as $district) {
                        echo "<option value='" . $district['District'] . "'>" . $district['District'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="sector">Sector:</label>
            <select class="form-control" id="sector" name="sector" required>
                <option value="">Select Sector</option>
                <?php 
                    // Dynamically populate the sector dropdown
                    foreach ($sectorOptions as $sector) {
                        echo "<option value='" . $sector['Sector'] . "'>" . $sector['Sector'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cell">Cell:</label>
            <select class="form-control" id="cell" name="cell" required>
                <option value="">Select Cell</option>
                <?php 
                    // Dynamically populate the cell dropdown
                    foreach ($cellOptions as $cell) {
                        echo "<option value='" . $cell['Cell'] . "'>" . $cell['Cell'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="village">Village:</label>
            <select class="form-control" id="village" name="village" required>
                <option value="">Select Village</option>
                <?php 
                    // Dynamically populate the village dropdown
                    foreach ($villageOptions as $village) {
                        echo "<option value='" . $village['Village'] . "'>" . $village['Village'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Submit</button>
            <button type="reset" class="btn btn-custom-clear">Clear</button>
        </div>
    </form>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>