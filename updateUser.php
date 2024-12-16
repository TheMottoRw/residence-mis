<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
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

if (!isset($_GET['userID'])) {
    die("User not specified.");
}

// Fetch userID from GET parameters 
$userID = $_GET['userID'];

// Fetch the user's current details
$stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("<div class='alert alert-danger'>User not found.</div>");
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

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE users SET Firstname = ?, Lastname = ?, Email = ?, Password = ?, Role = ?, ID = ?, Telephone = ?, Province = ?, District = ?, Sector = ?, Cell = ?, Village = ?, Status = ? WHERE UserID = ?");
    $stmt->bind_param("ssssssssssssss", $firstname, $lastname, $email, $password, $role, $id, $telephone, $province, $district, $sector, $cell, $village, $status, $userID);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'><center>User updated successfully</center>.</div>";
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
    <h2 class="text-center">Update User</h2>
    <form action="updateUser.php?userID=<?php echo $userID; ?>" method="POST">
        <div class="form-group">
            <label for="firstname">Firstname:</label>
            <input type="text" class="form-control" id="firstname" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed" name="firstname" value="<?php echo htmlspecialchars($user['Firstname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed" value="<?php echo htmlspecialchars($user['Lastname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="">Select User Role</option>
                <?php 
                    // Dynamically populate the role dropdown
                    foreach ($roleOptions as $role) {
                        $selected = ($role['RoleName'] == $user['Role']) ? 'selected' : '';
                        echo "<option value='" . $role['RoleName'] . "' $selected>" . $role['RoleName'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($user['ID']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['Telephone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="province">Province:</label>
            <select class="form-control" id="province" name="province" required>
                <option value="">Select Province</option>
                <?php 
                    // Dynamically populate the province dropdown
                    foreach ($provinceOptions as $province) {
                        $selected = ($province['Province'] == $user['Province']) ? 'selected' : '';
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
                        $selected = ($district['District'] == $user['District']) ? 'selected' : '';
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
                        $selected = ($sector['Sector'] == $user['Sector']) ? 'selected' : '';
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
                        $selected = ($cell['Cell'] == $user['Cell']) ? 'selected' : '';
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
                        $selected = ($village['Village'] == $user['Village']) ? 'selected' : '';
                        echo "<option value='" . $village['Village'] . "' $selected>" . $village['Village'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="1" <?php echo ($user['Status'] == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="2" <?php echo ($user['Status'] == 2) ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-custom">Update</button>
            <button type="reset" class="btn btn-custom-clear">Clear</button>
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

<?php
// Close database connection
$conn->close();
?>
