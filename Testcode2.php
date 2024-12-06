<?php include 'header.php'; ?> 
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

    // Check if the identifier is numeric (for ID search) or alphanumeric (for ResidentNo)
    if (is_numeric($identifier)) {
        // If identifier is numeric, it could be either ID or ResidentNo
        $sql = "SELECT * FROM Resident WHERE ID = ? OR Identifier = ?";
        $stmt = $conn->prepare($sql);

        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("ii", $identifier, $identifier); // Bind both ID and ResidentNo
    } else {
        // If identifier is alphanumeric, assume it's a ResidentNo (or other unique identifier)
        $sql = "SELECT * FROM Resident WHERE Identifier = ?";
        $stmt = $conn->prepare($sql);

        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $identifier); // Bind for string (ResidentNo)
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the resident exists
    if ($result->num_rows > 0) {
        $resident = $result->fetch_assoc();
    } else {
        $error_message = "<Font color='Red'>No citizen found with ID/Resident N<u>o</u>:<b> $identifier</b></font>";
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
    <title>Certificate</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Container Layout */
        .container {
            display: flex;
            justify-content: space-between;
            margin: 60px;
        }

        .left-container {
            width: 250px; /* Fixed width for the left container */
            padding: 20px;
            position: sticky;
            top: 0; /* Keeps the left container fixed at the top */
            height: 100vh;
            box-sizing: border-box;
        }

        .certificate-container {
            width: 70%; /* Increased width for the certificate container */
            max-width: 900px; /* Ensure the container doesn't exceed the page margin */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
            box-sizing: border-box;
        }

        .btn {
            background-color: Blue;  
            color: Black;  
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: Blue; 
        }

        /* Rules Container */
        .rules-container {
            margin-top: 0px;
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
        }

    </style>
</head>
<body>

<!-- Main Container with Left and Right Sections -->
<div class="container">
    <!-- Left Side Form and Rules -->
    <div class="left-container">
        <!-- Rules and Regulations Section -->
        <div class="rules-container">
            <h3><u>Rules and Regulations</u></h3>
            <ul>
                <li>The certificate is valid only if the data is accurate.</li>
                <li>Ensure to present the certificate for official verification when required.</li>
                <li>The certificate must be signed by an authorized official to be valid.</li>
                <li>The certificate is not transferable and must remain with the resident.</li>
                <li>If any information is incorrect, it must be reported immediately to the issuing authority.</li>
            </ul>
        </div>
        <br><br>
        <!-- Form Container -->
        <div class="form-container">
            <h4>Enter ResidentNo/ID</h4>
            <form action="certificate.php" method="POST">
                <label for="identifier"></label><br>
                <input type="text" id="identifier" name="identifier" required>
                <button type="submit" class="btn">Generate Certificate</button>
            </form>
        </div>
    </div>

    <!-- Right Side - Certificate Container -->
    <?php if ($resident || isset($error_message)): ?>
        <div class="certificate-container" id="certificate">
            <h2><b>REPUBLIC OF RWANDA </b></h2>
            <div class="certificate-header">
                <p><center><u><b>CERTIFICATE OF RESIDENCE</b></u></center></p>
            </div>

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
                    <p><strong>Address:</strong><br>
                        <?php echo htmlspecialchars($resident['Province'] . ', ' . $resident['District'] . ', ' . $resident['Sector'] . ', ' . $resident['Cell']); ?></p>
                <?php endif; ?>
            </div>

            <div class="certificate-footer">
                <p><strong>Issued By:</strong> Your Organization</p>
                <p class="date"><strong>Issued On:</strong> <?php echo date('F d, Y'); ?> &nbsp&nbsp&nbsp&nbsp and &nbsp&nbsp&nbsp&nbsp <strong>Validity:</strong> <?php echo date('F d, Y'); ?></p>
                <div class="signature">
                    <p><strong>Authorized By:</strong> <?php echo htmlspecialchars($authorizer_firstname . ' ' . $authorizer_lastname); ?></p>
                    <p><strong>Signature:</strong></p>
                    <p>________________________</p>
                    <p>Authorized Personnel</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    window.onload = function() {
        // Check if there's a resident or error message to download certificate
        <?php if ($resident): ?>
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Certificate content
            doc.setFontSize(16);
            doc.text("REPUBLIC OF RWANDA", 10, 10);
            doc.setFontSize(14);
            doc.text("CERTIFICATE OF RESIDENCE", 10, 20);
            doc.text("This certifies that:", 10, 30);
            doc.text("Name: <?php echo htmlspecialchars($resident['Firstname'] . ' ' . $resident['Lastname']); ?>", 10, 40);
            doc.text("DoB: <?php echo htmlspecialchars($resident['DoB']); ?>", 10, 50);
            doc.text("Gender: <?php echo htmlspecialchars($resident['Gender']); ?>", 10, 60);
            doc.text("Resident ID: <?php echo htmlspecialchars($resident['ID']); ?>", 10, 70);
            doc.text("Telephone: <?php echo htmlspecialchars($resident['Telephone']); ?>", 10, 80);
            doc.text("Father's Name: <?php echo htmlspecialchars($resident['FatherNames']); ?>", 10, 90);
            doc.text("Mother's Name: <?php echo htmlspecialchars($resident['MotherNames']); ?>", 10, 100);
            doc.text("Address: <?php echo htmlspecialchars($resident['Province'] . ', ' . $resident['District'] . ', ' . $resident['Sector'] . ', ' . $resident['Cell']); ?>", 10, 110);
            doc.text("Issued On: <?php echo date('F d, Y'); ?>", 10, 120);
            doc.text("Validity: <?php echo date('F d, Y'); ?>", 10, 130);
            doc.text("Authorized By: <?php echo htmlspecialchars($authorizer_firstname . ' ' . $authorizer_lastname); ?>", 10, 140);
            doc.text("Signature: ______________________", 10, 150);

            // Save the generated PDF automatically
            doc.save("certificate.pdf");
        <?php endif; ?>
    };
</script>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
