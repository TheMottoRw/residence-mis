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
    $province = $_POST['province'];
    $district = $_POST['district'];
    $sector = $_POST['sector'];
    $cell = $_POST['cell'];
    $village = $_POST['village'];
    $citizen_category = $_POST['citizen_category'];
    $houseno = $_POST['houseno'];
    $status = $_POST['status'];

    // Prepare and execute update statement
    $stmt = $conn->prepare("UPDATE resident SET 
    Firstname = ?, 
    Lastname = ?, 
    DoB = ?, 
    Telephone = ?, 
    Gender = ?, 
    ID = ?, 
    FatherNames = ?,
    MotherNames = ?,
    Province = ?, 
    District = ?, 
    Sector = ?, 
    Cell = ?, 
    Village = ?, 
    citizen_category = ?, 
    HouseNo = ?,
    Status = ?
    WHERE Identifier = ?");
    $stmt->bind_param('sssisissssssssssi', 
    $firstname,
    $lastname,
    $dob,
    $telephone,
    $gender,
    $id,
    $fathernames,
    $mothernames,
    $province,
    $district,
    $sector,
    $cell,
    $village,
    $citizen_category,
    $houseno,
    $status,
    $identifier
    );

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
<h2 class="text-center">Assign a House</h2>
    <form action="updateResident.php?identifier=<?php echo htmlspecialchars($record['Identifier']); ?>" method="POST">
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($record['Firstname']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($record['Lastname']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($record['DoB']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($record['Telephone']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="id">Gender:</label>
            <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($record['Gender']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($record['ID']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="district">MotherNames:</label>
            <input type="text" class="form-control" id="mothernames" name="mothernames" value="<?php echo htmlspecialchars($record['MotherNames']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="district">FatherNames:</label>
            <input type="text" class="form-control" id="fathernames" name="fathernames" value="<?php echo htmlspecialchars($record['FatherNames']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="houseno">House No:</label>
            <input type="text" class="form-control" id="houseno" name="houseno" value="<?php echo htmlspecialchars($record['HouseNo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="district">Province:</label>
            <input type="text" class="form-control" id="province" name="province" value="<?php echo htmlspecialchars($record['Province']); ?>"readonly required>
        </div>
        <div class="form-group">
            <label for="district">District:</label>
            <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($record['District']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="sector">Sector:</label>
            <input type="text" class="form-control" id="sector" name="sector" value="<?php echo htmlspecialchars($record['Sector']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="cell">Cell:</label>
            <input type="text" class="form-control" id="cell" name="cell" value="<?php echo htmlspecialchars($record['Cell']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="village">Village:</label>
            <input type="text" class="form-control" id="village" name="village" value="<?php echo htmlspecialchars($record['Village']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="gender">Citizen_Category:</label>
            <select class="form-control" id="citizen_category" name="citizen_category" required>
                <option value=""><?php echo htmlspecialchars($record['Citizen_Category']); ?></option>
                <option value="Normal" <?php echo $record['Citizen_Category'] == 'Normal' ? 'selected' : ''; ?>>Normal</option>
                <option value="Landlord" <?php echo $record['Citizen_Category'] == 'Landlord' ? 'selected' : ''; ?>>Landlord</option>
                <option value="VillageLeader" <?php echo $record['Citizen_Category'] == 'VillageLeader' ? 'selected' : ''; ?>>Village Leader</option>
                <option value="CellLeader" <?php echo $record['Citizen_Category'] == 'CellLeader' ? 'selected' : ''; ?>>Cell Leader</option>
                <option value="SectorLeader" <?php echo $record['Citizen_Category'] == 'SectorLeader' ? 'selected' : ''; ?>>Sector Leader</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value=""><?php echo htmlspecialchars($record['Status']); ?></option>
                <?php 
                    // Dynamically populate the status dropdown
                    foreach ($statusOptions as $status) {

                        echo "<option value='" . $status['Message'] . "'>" . $status['Message'] . "</option>";
                    }
                ?>
            </select>
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
