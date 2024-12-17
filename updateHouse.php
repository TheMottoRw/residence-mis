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
<h2 class="text-center">Update House's Info</h2>
    <form action="updateHouse.php?houseno=<?php echo htmlspecialchars($record['HouseNo']); ?>" method="POST">
    <div class="form-group">
            <label for="ownerid">House Owner:</label>
            <select class="form-control" pattern="[0-9]*" inputmode="numeric" title="Only numbers are allowed!" id="ownerid" name="ownerid" required>
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
            <select class="form-control" id="province" name="province" required>
                <option value="">Select Province</option>
                <?php 
                    // Dynamically populate the province dropdown
                    foreach ($provinceOptions as $province) {
                        $selected = ($province['Province'] == $record['Province']) ? 'selected' : '';
                        echo "<option value='" . $province['Province'] . "' $selected>" . $province['Province'] . "</option>";
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
                        $selected = ($district['District'] == $record['District']) ? 'selected' : '';
                        echo "<option value='" . $district['District'] . "' $selected>" . $district['District'] . "</option>";
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
                        $selected = ($sector['Sector'] == $record['Sector']) ? 'selected' : '';
                        echo "<option value='" . $sector['Sector'] . "' $selected>" . $sector['Sector'] . "</option>";
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
                        $selected = ($cell['Cell'] == $record['Cell']) ? 'selected' : '';
                        echo "<option value='" . $cell['Cell'] . "' $selected>" . $cell['Cell'] . "</option>";
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
                        $selected = ($village['Village'] == $record['Village']) ? 'selected' : '';
                        echo "<option value='" . $village['Village'] . "' $selected>" . $village['Village'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="1" <?php echo ($record['Status'] == 1) ? 'selected' : ''; ?>>Available</option>
                <option value="2" <?php echo ($record['Status'] == 2) ? 'selected' : ''; ?>>Destroyed</option>
            </select>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Update</button>
        </div>
    </form>
</div>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
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
