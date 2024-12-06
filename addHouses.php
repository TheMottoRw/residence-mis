<?php include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert New House</title>
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
            <input type="text" class="form-control" id="province" name="province" required>
        </div>
        <div class="form-group">
            <label for="district">District:</label>
            <input type="text" class="form-control" id="district" name="district" required>
        </div>
        <div class="form-group">
            <label for="sector">Sector:</label>
            <input type="text" class="form-control" id="sector" name="sector" required>
        </div>
        <div class="form-group">
            <label for="cell">Cell:</label>
            <input type="text" class="form-control" id="cell" name="cell" required>
        </div>
        <div class="form-group">
            <label for="village">Village:</label>
            <input type="text" class="form-control" id="village" name="village" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <input type="text" class="form-control" id="status" name="status" value="Available" required>
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
