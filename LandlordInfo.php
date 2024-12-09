<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Information</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .btn-btn-custom
        {
            background-color: #0056b3;
            border-radius:5px;
            text:white;
            padding: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<?php
// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("ID not specified.");
}

$id = $_GET['id'];

// Fetch the record from the database
$stmt = $conn->prepare("SELECT * FROM resident WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
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

<div class="container mt-5">
    <h2 class="text-center">Details Information of Landlord</h2>

    <!-- Display Landlord Information -->
    <div class="info-box">
        <div class="info-item">
            <strong>Firstname:</strong> <?php echo htmlspecialchars($record['Firstname']); ?>
        </div>
        <div class="info-item">
            <strong>Lastname:</strong> <?php echo htmlspecialchars($record['Lastname']); ?>
        </div>
        <div class="info-item">
            <strong>Date of Birth:</strong> <?php echo htmlspecialchars($record['DoB']); ?>
        </div>
        <div class="info-item">
            <strong>Telephone:</strong> <?php echo htmlspecialchars($record['Telephone']); ?>
        </div>
        <div class="info-item">
            <strong>Gender:</strong> <?php echo htmlspecialchars($record['Gender']); ?>
        </div>
        <div class="info-item">
            <strong>ID:</strong> <?php echo htmlspecialchars($record['ID']); ?>
        </div>
        <div class="info-item">
            <strong>Mother's Name:</strong> <?php echo htmlspecialchars($record['MotherNames']); ?>
        </div>
        <div class="info-item">
            <strong>Father's Name:</strong> <?php echo htmlspecialchars($record['FatherNames']); ?>
        </div>
        <div class="info-item">
            <strong>Province:</strong> <?php echo htmlspecialchars($record['Province']); ?>
        </div>
        <div class="info-item">
            <strong>District:</strong> <?php echo htmlspecialchars($record['District']); ?>
        </div>
        <div class="info-item">
            <strong>Sector:</strong> <?php echo htmlspecialchars($record['Sector']); ?>
        </div>
        <div class="info-item">
            <strong>Cell:</strong> <?php echo htmlspecialchars($record['Cell']); ?>
        </div>
        <div class="info-item">
            <strong>Village:</strong> <?php echo htmlspecialchars($record['Village']); ?>
        </div>
        <div class="info-item">
            <strong>Citizen Category:</strong> <?php echo htmlspecialchars($record['Citizen_Category']); ?>
        </div>
        <div class="info-item">
            <strong>House No:</strong> <?php echo htmlspecialchars($record['HouseNo']); ?>
        </div>
        <div class="info-item">
            <strong>Status:</strong> 
            <?php
                // Display the status message
                $statusMessage = $record['Status'];
                $statusFound = false;
                foreach ($statusOptions as $status) {
                    if ($status['Message'] == $statusMessage) {
                        echo htmlspecialchars($status['Message']);
                        $statusFound = true;
                        break;
                    }
                }
                // If the status doesn't exist in the status options, just display the current status
                if (!$statusFound) {
                    echo htmlspecialchars($statusMessage);
                }
            ?>
        </div>
        <div class="form-group text-center">
           <a href="houses.php"> <button type="" class="btn-btn-custom"><font color='White'>Close</button></a>
        </div>
    </div>
</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
