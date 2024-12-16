<?php
include 'connect.php';
include_once "includes/session_manager.php";
session_start();
session_controller();
?>
<?php 
include 'connect.php';  // Database connection

// Check if Identifier is provided in the URL
if (!isset($_GET['identifier']) || empty($_GET['identifier'])) {
    // Display a user-friendly message and stop execution
    echo "<div class='alert alert-danger'>Citizen identifier is missing or invalid.</div>";
    exit();
}

$identifier = $_GET['identifier'];  // Capture Identifier from URL

// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SELECT query to check if the Citizen exists
$stmt = $conn->prepare("SELECT * FROM Resident WHERE Identifier = ?");
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();

// If the Citizen is not found, show an error
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Citizen not found with Identifier: " . htmlspecialchars($identifier) . "</div>";
    exit();
}

// Prepare and execute DELETE query to delete the house
$stmt = $conn->prepare("DELETE FROM Resident WHERE Identifier = ?");
$stmt->bind_param("s", $identifier);

if ($stmt->execute()) {
    // Successful deletion
    echo "<div class='alert alert-success'><center>Citizen deleted successfully!</center></div>";
    header("Location:ListOfCitizens.php");  // Redirect to the Citizen list after deletion
    exit();
} else {
    // If deletion fails
    echo "<div class='alert alert-danger'>Error deleting Citizen: " . $stmt->error . "</div>";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
