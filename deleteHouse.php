<?php
include 'connect.php';
include_once "includes/session_manager.php";
session_start();
session_controller();
?>
<?php
include 'connect.php';  // Database connection

// Check if HouseNo is provided in the URL
if (!isset($_GET['houseno']) || empty($_GET['houseno'])) {
    die("HouseNo not specified.");
}

$houseno = $_GET['houseno'];  // Capture HouseNo from URL

// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SELECT query to check if the house exists
$stmt = $conn->prepare("SELECT * FROM houses WHERE HouseNo = ?");
$stmt->bind_param("s", $houseno);
$stmt->execute();
$result = $stmt->get_result();

// If the house is not found, show an error
if ($result->num_rows == 0) {
    die("House not found with HouseNo: " . htmlspecialchars($houseno));
}

// Prepare and execute DELETE query to delete the house
$stmt = $conn->prepare("DELETE FROM houses WHERE HouseNo = ?");
$stmt->bind_param("s", $houseno);

if ($stmt->execute()) {
    // Successful deletion
    echo "<div class='alert alert-success'><center>House deleted successfully!</center></div>";
    header("Location:houses.php");  // Redirect to the house list after deletion
    exit();
} else {
    // If deletion fails
    echo "<div class='alert alert-danger'>Error deleting house: " . $stmt->error . "</div>";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
