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

$identifier = $_GET['identifier'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT t.*,p.Province as ProvinceName,d.District as DistrictName,s.Sector as SectorName,c.Cell as CellName,v.Village as VillageName FROM resident t  INNER JOIN provinces p ON t.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=t.District INNER JOIN sectors s ON s.SectorID=t.Sector INNER JOIN cells c ON c.CellID=t.Cell INNER JOIN villages v ON v.VillageID=t.Village WHERE Identifier = ?");
$stmt->bind_param("i", $identifier);
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

    $sql = "SELECT * FROM  houses t WHERE HouseNo = ?";
    $stmt = $conn->prepare($sql);
    
    // Check if prepare() failed
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $houseno);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    if($result->num_rows==0)
{
    echo "<script>alert('House not found');window.location=window.location.href;</script>";
    exit();
}
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
            <input type="text" class="form-control" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed" id="firstname" name="firstname" value="<?php echo htmlspecialchars($record['Firstname']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="lastname">Lastname:</label>
            <input type="text" class="form-control" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed!" id="lastname" name="lastname" value="<?php echo htmlspecialchars($record['Lastname']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($record['DoB']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" class="form-control" id="telephone" pattern="[0-9]*" inputmode="numeric" title="Only numbers are aloowed!" name="telephone" value="<?php echo htmlspecialchars($record['Telephone']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="id">Gender:</label>
            <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($record['Gender']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" pattern="[0-9]*" inputmode="numeric" title="Only numbers are aloowed!" name="id" value="<?php echo htmlspecialchars($record['ID']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="district">MotherNames:</label>
            <input type="text" class="form-control" id="mothernames" name="mothernames" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed!" value="<?php echo htmlspecialchars($record['MotherNames']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="district">FatherNames:</label>
            <input type="text" class="form-control" id="fathernames" name="fathernames" pattern="[A-Za-z]*" inputmode="alphabetic" title="Only alphabetic characters are allowed!" value="<?php echo htmlspecialchars($record['FatherNames']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="houseno">House No:</label>
            <input type="text" class="form-control" id="houseno" name="houseno" value="<?php echo htmlspecialchars($record['HouseNo']); ?>">
        </div>
        <div class="form-group">
            <label for="district">Province:</label>
            <input type="hidden" id="province" name="province" value="<?php echo htmlspecialchars($record['Province']); ?>">
            <input type="text" class="form-control" id="provinceName" name="provinceName" value="<?php echo htmlspecialchars($record['ProvinceName']); ?>"readonly required>
        </div>
        <div class="form-group">
            <label for="district">District:</label>
            <input type="hidden" id="district" name="district" value="<?php echo htmlspecialchars($record['District']); ?>">
            <input type="text" class="form-control" id="districtName" name="districtName" value="<?php echo htmlspecialchars($record['DistrictName']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="sector">Sector:</label>
            <input type="hidden" id="sector" name="sector" value="<?php echo htmlspecialchars($record['Sector']); ?>">
            <input type="text" class="form-control" id="sectorName" name="sectorName" value="<?php echo htmlspecialchars($record['SectorName']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="cell">Cell:</label>
            <input type="hidden" id="cell" name="cell" value="<?php echo htmlspecialchars($record['Cell']); ?>">
            <input type="text" class="form-control" id="cellName" name="cellName" value="<?php echo htmlspecialchars($record['CellName']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="village">Village:</label>
            <input type="hidden" id="village" name="village" value="<?php echo htmlspecialchars($record['Village']); ?>">
            <input type="text" class="form-control" id="villageName" name="villageName" value="<?php echo htmlspecialchars($record['VillageName']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="gender">Citizen_Category:</label>
            <select class="form-control" id="citizen_category" name="citizen_category" required>
                <option value=""><?php echo htmlspecialchars($record['Citizen_Category']); ?></option>
                <option value="Normal" <?php echo $record['Citizen_Category'] == 'Normal' ? 'selected' : ''; ?>>Normal</option>
                <option value="Landlord" <?php echo $record['Citizen_Category'] == 'Landlord' ? 'selected' : ''; ?>>Landlord</option>
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
<script>
    window.addEventListener("DOMContentLoaded",function(){
        document.querySelector("#houseno").addEventListener("blur",function(){
            loadByHouse(document.querySelector("#houseno").value);
        })
    })
    async function loadByHouse(houseNo){
        const house = await fetch("helper/api.php?find=byHouseNo&house_no="+houseNo,{
            headers:{
                "Content-Type":"application/json"
            }
        })
        .then(response=>response.json())
        .then(result=>result)
        .catch(error=>console.log(error));
        if(house!=null){
            let obj = house;
            document.querySelector("#province").value = obj.Province;
            document.querySelector("#provinceName").value = obj.ProvinceName;
            document.querySelector("#districtName").value = obj.DistrictName;
            document.querySelector("#district").value = obj.District;
            document.querySelector("#sector").value = obj.Sector;
            document.querySelector("#sectorName").value = obj.SectorName;
            document.querySelector("#cell").value = obj.Cell;
            document.querySelector("#cellName").value = obj.CellName;
            document.querySelector("#village").value = obj.Village;
            document.querySelector("#villageName").value = obj.VillageName;

        }else{alert("House not found");}
    }
</script>
</body>
</html>
