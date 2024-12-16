<?php
include 'connect.php';
include_once "includes/session_manager.php";
session_start();
session_controller();
?>
<?php 
include 'connect.php';  // Database connection

// Check if Identifier is provided in the URL
if (!isset($_GET['roleid']) || empty($_GET['roleid'])) {
    // Display a user-friendly message and stop execution
    echo "<div class='alert alert-danger'> Roleid is missing or invalid.</div>";
    exit();
}

$roleid = $_GET['roleid'];  // Capture Identifier from URL

// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SELECT query to check if the Citizen exists
$stmt = $conn->prepare("SELECT * FROM Roles WHERE RoleID = ?");
$stmt->bind_param("s", $roleid);
$stmt->execute();
$result = $stmt->get_result();

// If the Citizen is not found, show an error
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Citizen not found with RoleID: " . htmlspecialchars($roleid) . "</div>";
    exit();
}

// Prepare and execute DELETE query to delete the house
$stmt = $conn->prepare("DELETE FROM Roles WHERE RoleID = ?");
$stmt->bind_param("s", $roleid);

if ($stmt->execute()) {
    // Successful deletion
    echo "<div class='alert alert-success'><center>Role is deleted successfully!</center></div>";
    header("Location:Role.php");  // Redirect to the Citizen list after deletion
    exit();
} else {
    // If deletion fails
    echo "<div class='alert alert-danger'>Error deleting Role: " . $stmt->error . "</div>";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
