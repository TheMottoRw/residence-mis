<?php   
session_start(); // Ensure that the session is started

include 'connect.php'; // Include database connection
$resident = null;
$authorizer_firstname = "";
$authorizer_lastname = "";

// Check if the user is logged in and retrieve the authorizer's name
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch authorizer's details from the user table
    $sql = "SELECT Firstname, Lastname FROM users WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    
    // Check if prepare() failed
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $authorizer = $result->fetch_assoc();
        $authorizer_firstname = $authorizer['Firstname'];
        $authorizer_lastname = $authorizer['Lastname'];
    } else {
        $error_message = "Logged-in user not found.";
    }

    // Close the database connection for the user query
    $stmt->close();
}

// Check if the form is submitted with an identifier
if (isset($_POST['identifier'])) {
    $identifier = $_POST['identifier'];
    $dob = $_POST['dob'];
    echo $dob;

    // Check if the identifier is numeric (for ID search) or alphanumeric (for ResidentNo)
    if (is_numeric($identifier)) {
        // If identifier is numeric, it could be either ID or ResidentNo
        $sql = "SELECT * FROM resident WHERE (ID = ? OR Identifier = ?) AND DoB = ?";
        $stmt = $conn->prepare($sql);

        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("iis", $identifier, $identifier,$dob); // Bind both ID and ResidentNo
    } else {
        // If identifier is alphanumeric, assume it's a ResidentNo (or other unique identifier)
        $sql = "SELECT * FROM resident WHERE Identifier = ? AND DoB = ?";
        $stmt = $conn->prepare($sql);

        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("ss", $identifier,$dob); // Bind for string (ResidentNo)
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the resident exists
    if ($result->num_rows > 0) {
        $resident = $result->fetch_assoc();
    } else {
        $error_message = "<Font color='Red'><B>No citizen found with ID/Resident N<u>o</u></B>:<b> $identifier</b></font>";
    }

    // Close the database connection for the resident query
    $stmt->close();
    $conn->close();
}

?>

<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Residence Management System</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Header Section */
        header {
            background-color: #3498db;
            color: white;
            text-align: center;
            padding: 20px 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 60px;
        }

        .left-container {
            width: 500px; /* Fixed width for the left container */
            padding: 20px;
            position: sticky;
            top: 0; /* Keeps the left container fixed at the top */
            height: 100vh;
            box-sizing: border-box;
            margin: 20px;
        }

        .certificate-container {
            width: 600px; /* Increased width for the certificate container */
            max-width: 900px; /* Ensure the container doesn't exceed the page margin */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 80px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #3498db;  
            color: white;  
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .rules-container {
            margin-top: 20px;
            background-color: skyblue;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .form-container {
            margin-top: 20px;
            background-color: skyblue;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .certificate-header p {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .certificate-footer {
            margin-top: 20px;
            text-align: left;
        }

        .signature p {
            margin-top: 10px;
        }

        /* Print-specific CSS */
        @media print {
            body * {
                visibility: hidden;
            }
            .certificate-container, .certificate-container * {
                visibility: visible;
            }
            .certificate-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 900px;
            }

            /* Hide the download link when printing */
            .certificate-container .btn {
                display: none;
            }
        }

    </style>
</head>
<body>

<header>
    <h1>Citizen's Residence Management System (CRMS)</h1>
    <p>Request your Residence Certificate here. After approval, you can download it. For system users, you can click here to <a href="login.php"><font color="black"><h2>Login</h2></a></font>||<a href="index.php"><font color="black">Back to Homepage</a></font></p>
</header>

<!-- Main Container with Left and Right Sections -->
<div class="container">

    <!-- Right Side - Certificate Container -->
    <?php if ($resident || isset($error_message)): ?>
        <div class="certificate-container">
            <p><h2><b>REPUBLIC OF RWANDA </b></h2></p>
            <p><img src="images/National.jpg" width="150" height="150"></p><br>

            <div class="certificate-header">
                <p><center><u><b>CERTIFICATE OF RESIDENCE</b></u></center></p>
            </div>
            <?php
            if(isset($_GET['CertificateRequestSubmit'])){
                $residentId = $_POST['ResidentId'];
                $residentNo = $_POST['ResidentNo'];
                $sql = "INSERT INTO certificate_requests SET ID = ?, ResidentNo = ?";
                $stmt = $conn->prepare($sql);

                // Check if prepare() failed
                if ($stmt === false) {
                    die('MySQL prepare error: ' . $conn->error);
                }

                $stmt->bind_param("ii", $residentId,$residentNo);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'><center>Request sent successful</center>.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }}
            ?>

            <div class="certificate-body">
                <?php if (isset($error_message)): ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php else: ?>
                    <p><strong>This certifies that:</strong></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($resident['Firstname'] . ' ' . $resident['Lastname']); ?></p>
                    <p><strong>DoB:</strong> <?php echo htmlspecialchars($resident['DoB']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($resident['Gender']); ?></p>
                    <p><strong>Resident ID:</strong> <?php echo htmlspecialchars($resident['ID']); ?></p>
                    <p><strong>Telephone:</strong> <?php echo htmlspecialchars($resident['Telephone']); ?></p>
                    <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($resident['FatherNames']); ?></p>
                    <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($resident['MotherNames']); ?></p>
                    <p><strong>Resides at</strong><br>
                        <?php echo htmlspecialchars($resident['Province'] . ', ' . $resident['District'] . ', ' . $resident['Sector'] . ', ' . $resident['Cell']); ?></p>
                <?php endif; ?>
            </div>

            <div class="certificate-footer">
                <p><strong>Issued By:</strong> LODA</p>
                <p class="date"><strong>Application Date:</strong> <?php echo date('F d, Y'); ?></p>
                </div>
            
            <!-- Download certificate button will be shown after approval -->
            <form method="POST" action="certificateRequest.php?CertificateRequestSubmit">
                <input type="hidden" name="ResidentId" value="<?= $resident['ID']; ?>">
                <input type="hidden" name="ResidentNo" value="<?= $resident['Identifier']; ?>">
                <input type="submit" class="btn" value="Approval Request">
            </form>
        </div>
    <?php endif; ?>

</div>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
