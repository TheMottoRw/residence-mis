
<?php include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Resident</title>
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
            background-color: #007bff;
            border-color: #007bff;
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

// Function to generate a unique 8-digit ResidentNo
function generateIdentifier($conn) {
    // Try to generate a unique 8-digit number
    do {
        $identifier = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        // Check if this number already exists in the database
        $result = $conn->query("SELECT COUNT(*) FROM resident WHERE Identifier = '$identifier'");
        $row = $result->fetch_row();
        $exists = $row[0] > 0;
    } while ($exists); // Keep generating if the number already exists

    return $identifier;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob']; // Date format is already YYYY-MM-DD from the input
    $telephone = $_POST['telephone'];
    $gender = $_POST['gender'];
    $id = $_POST['id'];
    $fathernames = $_POST['fathernames'];
    $mothernames = $_POST['mothernames'];

    // Generate the ResidentNo automatically
    $identifier = generateIdentifier($conn); // Get a unique 8-digit ResidentNo

    // Validate ResidentNo (ensure it is 8 digits)
    if (!preg_match("/^\d{8}$/", $identifier)) {
        echo "<div class='alert alert-danger'><center>Resident No. should be exactly 8 digits.</center></div>";
    } else {
        // Prepare and execute insert statement (removed 'Identifier' from the query)
        $stmt = $conn->prepare("INSERT INTO resident (Identifier,Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames) 
                                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the query preparation was successful
        if ($stmt === false) {
            echo "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
        } else {
            // Bind the parameters to the prepared statement
            $stmt->bind_param("issssssss",$identifier, $firstname, $lastname, $dob, $telephone, $gender, $id, $fathernames, $mothernames);

            // Execute the prepared statement
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'><center>New record created successfully. Resident No: $identifier</center>.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            // Close the statement
            $stmt->close();
        }
    }
}
/*// Fetch available statuses from the Message table
$statusOptions = [];
$sql = "SELECT Message FROM status"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusOptions[] = $row; // Store StatusID and Message in an array
    }
}
// Fetch available HouseNo from the Message table
$housenoOptions = [];
$sql = "SELECT HouseNo FROM houses"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $housenoOptions[] = $row; // Store HouseNo and Message in an array
    }
}
// Fetch available Provinces from the Message table
$provinceOptions = [];
$sql = "SELECT Province FROM provinces"; // Query the province table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $provinceOptions[] = $row; // Store provinces in an array
    }
}
// Fetch available districts from the Message table
$districtOptions = [];
$sql = "SELECT District FROM districts inner JOIN provinces ON districts.ProvinceID= Provinces.ProvinceID;"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $districtOptions[] = $row; // Store districts and Message in an array
    }
}
// Fetch available districts from the Message table
$sectorOptions = [];
$sql = "SELECT Sector FROM sectors"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sectorOptions[] = $row; // Store Sector and Message in an array
    }
}
// Fetch available cells from the Message table
$cellOptions = [];
$sql = "SELECT Cell FROM cells"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cellOptions[] = $row; // Store Cells and Message in an array
    }
}
// Fetch available villages from the Message table
$villageOptions = [];
$sql = "SELECT Village FROM villages"; // Query the Status table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $villageOptions[] = $row; // Store villages and Message in an array
    }
}*/
?>

<div class="container mt-5 form-container">
    <h2 class="text-center">Insert New Citizen</h2>
    <form action="addResident.php" method="POST">
    <div class="form-group">
            <label for="firstname">ResidentNo:</label>
            <input type="text" class="form-control" id="identifier" name="identifier" value="<?php echo generateIdentifier($conn); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Father_names:</label>
            <input type="text" class="form-control" id="fathernames" name="fathernames" required>
        </div>
        <div class="form-group">
            <label for="lastname">Mother_names:</label>
            <input type="text" class="form-control" id="mothernames" name="mothernames" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" name="telephone">
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id">
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
