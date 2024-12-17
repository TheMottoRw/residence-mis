<?php include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert New House</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link href="media/select2.min.css" rel="stylesheet" />
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the HouseNo format
    $houseno = $_POST['houseno'];
    if (!preg_match("/^HS\d{6}$/", $houseno)) {
        echo "<div class='alert alert-danger'><center>Invalid House Number format. It should be in the format HS000001.</center></div>";
    } else {
        // Retrieve other form data
        $province = $_POST['province'];
        $district = $_POST['district'];
        $sector = $_POST['sector'];
        $cell = $_POST['cell'];
        $village = $_POST['village'];
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Prepare and execute insert statement
        $stmt = $conn->prepare("INSERT INTO houses (HouseNo, Province, District, Sector, Cell, Village, ID, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if prepare() failed
        if ($stmt === false) {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        } else {
            // Bind parameters
            $stmt->bind_param("ssssssss", $houseno, $province, $district, $sector, $cell, $village, $id, $status);

            // Execute statement and check for errors
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'><center>New house added successfully with HouseNo: $houseno</center></div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
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
?>

<div class="container mt-5 form-container">
    <h2 class="text-center">House Registration</h2>
    <form action="addHouses.php" method="POST">
        <div class="form-group">
            <label for="houseno">House No:</label>
            <input type="text" class="form-control" id="houseno" name="houseno" value="<?php echo generateHouseNo($conn); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="ownerid">OwnerID:</label>
            <select class="form-control" id="id" name="id" required>
                <option value="">Choose a House Owner</option>
                <?php 
                    // Dynamically populate the Owner ID dropdown
                    foreach ($idOptions as $id) {
                        echo "<option value='" . $id['ID'] . "'>" . $id['ID'] . "</option>";
                    }
                ?>
            </select>
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
            <label for="status"></label>
            <input type="Hidden" class="form-control" id="status" name="status" value="Available" required>
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
    $(document).ready(function() {
      $('#id').select2({
        placeholder: 'Search House Owner...',
        allowClear: true
      });
    });
  </script>
  <script>
    window.addEventListener("DOMContentLoaded",function(){
        loadProvinces();
        document.querySelector("#province").onchange=function(){
            loadDistrict(document.querySelector("#province").value);
        }
        document.querySelector("#district").onchange=function(){
            loadSector(document.querySelector("#district").value);
        }
        document.querySelector("#sector").onchange=function(){
            loadCell(document.querySelector("#sector").value);
        }
        document.querySelector("#cell").onchange=function(){
            loadVillage(document.querySelector("#cell").value);
        }
    })
    var reqOptions = {
        headers:{
            "Content-Type":"application/json"
        }
    }
    async function loadProvinces(){
        const data = await fetch("helper/api.php?find=province",reqOptions)
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        console.log(data);
        setAdministrativeSelect("province",data,"Province");
    }
    async function loadDistrict(id){
        const data = await fetch("helper/api.php?find=district&province="+id,reqOptions)
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        setAdministrativeSelect("district",data,"District");
    }
    async function loadSector(id){
        const data = await fetch("helper/api.php?find=sector&district="+id,reqOptions)
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        setAdministrativeSelect("sector",data,"Sector");
    }
    async function loadCell(id){
        const data = await fetch("helper/api.php?find=cell&sector="+id,reqOptions)
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        setAdministrativeSelect("cell",data,"Cell");
    }
    async function loadVillage(id){
        const data = await fetch("helper/api.php?find=village&cell="+id,reqOptions)
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        setAdministrativeSelect("village",data,"Village");
    }
    function setAdministrativeSelect(el,arr,keyElement){
        let options = "<option value='0'>Select </option>";
        for(let i=0;i<arr.length;i++){
            let keyElementId = keyElement+"ID";
            options+=`<option value=${arr[i][keyElementId]}>${arr[i][keyElement]}</option>`;
        }
        console.log(arr);
        document.getElementById(el).innerHTML = options;
    }
 </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
