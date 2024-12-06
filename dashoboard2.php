<?php include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>
        This is village leader dashoard
    </h2>
    <?php
$userCell = $_SESSION["cell"];

// Check if $userCell is set and is a numeric value
if (isset($userCell)) {
    // Fetch the record from the database
    $stmt = $conn->prepare("SELECT * FROM resident WHERE Cell = ?");
    $stmt->bind_param("i", $userCell);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["Cell"] == $userCell) { // Ensure strict comparison here
                echo htmlspecialchars($row["Cell"]) . "<br>";
            }
        }
    } else {
        echo "Record not found.";
    }
} else {
    echo "Invalid cell value in session.";
}

           ?>
   
</body>

</html>