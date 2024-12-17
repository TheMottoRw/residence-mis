<?php
include 'connect.php';
include_once "includes/session_manager.php";
session_start();
session_controller();
?>
<?php 
include 'connect.php';  // Database connection

// Check if Identifier is provided in the URL
if (!isset($_GET['userID']) || empty($_GET['userID'])) {
    // Display a user-friendly message and stop execution
    echo "<div class='alert alert-danger'> userID is missing or invalid.</div>";
    exit();
}

$userID = $_GET['userID'];  // Capture Identifier from URL

// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SELECT query to check if the Citizen exists
$stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

// If the Citizen is not found, show an error
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>User not found with UserID: " . htmlspecialchars($userID) . "</div>";
    exit();
}

// Prepare and execute DELETE query to delete the house
$stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
$stmt->bind_param("s", $userID);

if ($stmt->execute()) {
    // Successful deletion
    echo "<div class='alert alert-success'><center>User deleted successfully!</center></div>";
    header("Location:users.php");  // Redirect to the Citizen list after deletion
    exit();
} else {
    // If deletion fails
    echo "<div class='alert alert-danger'>Error deleting Citizen: " . $stmt->error . "</div>";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
