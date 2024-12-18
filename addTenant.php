<?php
include_once "helper/DbApi.php";
include 'header.php';
$dbApi = new DbApi();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add tenant</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link href="media/select2.min.css" rel="stylesheet"/>
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
    <!-- dropdown filter-->
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

// Function to generate next HouseNo in the format HS000001
function generateHouseNo($conn)
{
    // Get the last HouseNo from the database
    $sql = "SELECT HouseNo FROM houses ORDER BY HouseNo DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastHouseNo = $row['HouseNo'];
        // Extract the number part and increment it
        $numberPart = substr($lastHouseNo, 2); // Remove 'HS' prefix
        $newNumberPart = str_pad($numberPart + 1, 6, "0", STR_PAD_LEFT); // Increment and pad with leading zeros
        return 'HS' . $newNumberPart;
    } else {
        // If no houses exist, start with HS000001
        return 'HS000001';
    }
}

function generateIdentifier($conn)
{
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
    $houseno = $_POST['houseno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob']; // Date format is already YYYY-MM-DD from the input
    $telephone = $_POST['telephone'];
    $gender = $_POST['gender'];
    $id = $_POST['id'];
    $fathernames = $_POST['fathernames'];
    $mothernames = $_POST['mothernames'];
    $password = md5($_POST['password']);
    $status = $_POST['status'];

// Generate the ResidentNo automatically
    $identifier = generateIdentifier($conn); // Get a unique 8-digit ResidentNo
    $houseInfo = $dbApi->findHouseByNo($houseno);
    $resident = $dbApi->findResidentByID($id);
// Validate ResidentNo (ensure it is 8 digits)
    if (!preg_match("/^\d{8}$/", $identifier)) {
        echo "<div class='alert alert-danger'><center>Resident No. should be exactly 8 digits.</center></div>";
    } else {
        //check user exist
        if ($resident != null) {
            $resident = json_decode($resident, true);
            if ($houseInfo == null) {
                echo "<script>alert('House does not exist');</script>";
                header("location: addTenant.php");
            }
            $houseInfo = json_decode($houseInfo, true);
//            clear recent assignment
            $stmt0 = $conn->prepare("UPDATE resident SET HouseNo=null,Status='Pending' WHERE HouseNo=?");
            $stmt0->bind_param("s", $houseno);
            $stmt0->execute();
            //update user with house no
            $stmt = $conn->prepare("UPDATE resident SET HouseNo=?,Province=?,District=?,Sector=?,Cell=?,Village=?,Status='Available' WHERE ID=?");
            $stmt->bind_param("siiiiii", $houseno, $houseInfo['Province'], $houseInfo['District'], $houseInfo['Sector'], $houseInfo['Cell'], $houseInfo['Village'], $resident['ID']);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'><center>Tenant added successful</center>.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Prepare and execute insert statement (removed 'Identifier' from the query)
            $stmt = $conn->prepare("INSERT INTO resident (Identifier,Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames, Password,Status) 
                                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");
            // Bind the parameters to the prepared statement
            $stmt->bind_param("issssssssss", $identifier, $firstname, $lastname, $dob, $telephone, $gender, $id, $fathernames, $mothernames, $password, $status);
            $stmt->execute();
            $resident = $dbApi->findResidentByID($identifier);
            if ($houseInfo == null) {
                echo "<script>alert('House does not exist');</script>";
                header("location: addTenant.php");
            }
            $houseInfo = json_decode($houseInfo, true);
            //update user with house no
            $stmt1 = $conn->prepare("UPDATE resident SET HouseNo=?,Province=?,District=?,Sector=?,Cell=?,Village=?,Status='Available' WHERE identifier=?");
            $stmt1->bind_param("siiiiii", $houseno, $houseInfo['Province'], $houseInfo['District'], $houseInfo['Sector'], $houseInfo['Cell'], $houseInfo['Village'], $resident['ID']);
            // Execute the prepared statement
            if ($stmt1->execute()) {
                echo "<div class='alert alert-success'><center>Tenant added successful. Resident No: $identifier</center>.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            // Close the statement
            $stmt->close();
            $stmt1->close();
        }
    }

// Fetch available ID from the Residents ID table
    $idOptions = [];
    $sql = "SELECT ID FROM resident"; // Query the Resident table
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idOptions[] = $row; // Store OwnerID in an array
        }
    }
// Fetch available Provinces
    $provinceOptions = [];
    $sql = "SELECT Province FROM provinces"; // Query the province table
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $provinceOptions[] = $row; // Store province in an array
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
}
?>

<div class="container mt-5 form-container">
    <h2 class="text-center">Tenant registration</h2>
    <form action="addTenant.php" method="POST" id="form">
        <div class="form-group">
            <label for="firstname">House No:</label>
            <input type="text" class="form-control" id="houseno" name="houseno" required>
        </div>
        <div class="form-group">
<!--            <label for="firstname">ResidentNo:</label>-->
            <input type="hidden" class="form-control" id="identifier" name="identifier"
                   value="<?php echo generateIdentifier($conn); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="id">ID Number:</label>
            <input type="text" class="form-control" id="id" name="id" pattern="[0-9]*" inputmode="numeric"
                   title="Only numbers are allowed!">
        </div>
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" pattern="[A-Za-z]*" inputmode="alphabetic"
                   title="Only alphabetic characters are allowed" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" pattern="[A-Za-z]*" inputmode="alphabetic"
                   title="Only alphabetic characters are allowed" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Father_names:</label>
            <input type="text" class="form-control" pattern="[A-Za-z ]*" inputmode="alphabetic"
                   title="Only alphabetic characters are allowed" id="fathernames" name="fathernames" required>
        </div>
        <div class="form-group">
            <label for="lastname">Mother_names:</label>
            <input type="text" class="form-control" pattern="[A-Za-z ]*" inputmode="alphabetic"
                   title="Only alphabetic characters are allowed" id="mothernames" name="mothernames" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" pattern="[0-9]*" inputmode="numeric"
                   title="Only numbers are allowed!" name="telephone">
        </div>
        <div class="form-group">
            <label for="passsword">Default password:</label>
            <input type="password" class="form-control" id="password" name="password">
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
            <label for="id">Status:</label>
            <input type="text" class="form-control" id="status" name="status" value="Pending">
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Submit</button>
            <button type="reset" class="btn btn-custom-clear">Clear</button>
        </div>
    </form>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>

<script src="media/jquery-360.min.js"></script>
<script src="media/select2.min.js"></script>
<script>
    // Initialize Select2 for the select box
    $(document).ready(function () {
        // $('#id').select2({
        //     placeholder: 'Search House Owner...',
        //     allowClear: true
        // });
    });
</script>
<script>
    window.addEventListener("DOMContentLoaded", function () {
        document.querySelector("#id").addEventListener('blur', function () {
            loadResidentInformation(document.querySelector("#id").value);
        });
    })
    var reqOptions = {
        headers: {
            "Content-Type": "application/json"
        }
    }

    async function loadResidentInformation(residentId) {
        const data = await fetch(`helper/api.php?find=byResidentNo&residentno=${residentId}`, reqOptions)
            .then(response => response.json())
            .then(result => result)
            .catch(error => console.log(error));
        console.log(data);
        if(data!=null){
            setResidentInfo(data);
        }else{
            clearForm();
        }
    }

    function setResidentInfo(obj) {
        document.querySelector("#id").value=obj.ID;
       document.querySelector("#firstname").value=obj.Firstname;
       document.querySelector("#lastname").value=obj.Lastname;
       document.querySelector("#telephone").value=obj.Telephone;
       document.querySelector("#fathernames").value=obj.FatherNames;
       document.querySelector("#mothernames").value=obj.MotherNames;
       document.querySelector("#dob").value=obj.DoB;
       document.querySelector("#gender").value=obj.Gender;
    }
    function clearForm(){
        document.querySelector("#id").value='';
        document.querySelector("#firstname").value='';
        document.querySelector("#lastname").value='';
        document.querySelector("#telephone").value='';
        document.querySelector("#fathernames").value='';
        document.querySelector("#mothernames").value='';
        document.querySelector("#mothernames").value='';
        document.querySelector("#dob").value='';
    }
</script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
