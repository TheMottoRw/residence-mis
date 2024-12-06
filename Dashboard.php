<?php
include 'connect.php';
include_once "includes/session_manager.php";
session_start();
session_controller();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Residence Dashboard</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
    /* Basic layout styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        display: flex;
        margin-top: 10px;
    }

    /* Left section for brief information */
    .left-info {
        width: 30%;
        /* Takes up 30% of the page width */
        padding: 20px;
        background-color: skyblue;
        border-right: 2px solid #ddd;
        margin-top: 0px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .left-info h4 {
        margin-top: 0;
        color: #333;
    }

    .left-info p {
        color: #666;
    }

    /* Dashboard section takes up remaining space */
    .dashboard {
        width: 70%;
        padding: 20px;
        margin-top: 0px;
        margin-bottom: 20px;
    }

    /* Footer styles */
    .footer {
        width: 100%;
        background-color: skyblue;
        color: black;
        text-align: center;
        padding: 10px 0;
        position: relative;
        bottom: 0;
    }

    .footer h6 {
        margin: 0;
        font-size: 14px;
    }
    </style>
</head>

<body>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>

    <?php
     $userRole = $_SESSION["role"];
     if($userRole == "Admin"){
        include 'header.php'; 
        ?>
    <!-- Main container with two sections: Left info and Dashboard -->
    <div class="container">
        <!-- Left section with information about the system -->
        <div class="left-info">
            <h4>About the Citizen Residence System</h4>


            <p>
                The Citizen’s Residence Management System (CRMS) is a digital platform designed to
                efficiently manage citizen residence information in urban and rural areas, thereby
                improving city and rural services and community engagement. It features resident
                registration, address verification, demographic data collection, and service requests,
                all accessible through a user-friendly web interface.
            </p>
            <p>
                The primary objective of CRMS is to streamline the administration of residential data,
                which includes managing resident registrations, verifying details, and securely storing
                information in compliance with local regulations and zoning laws.

            </p>
        </div>

        <!-- Right section: Dashboard with data from the database -->
        <div class="dashboard">
            <h3>Dashboard</h3>
            <?php
        // Fetch resident status counts
        $resident_counts_sql = "SELECT Status, COUNT(*) as count FROM resident GROUP BY Status";
        $resident_counts_result = $conn->query($resident_counts_sql);

        if ($resident_counts_result) {
            $resident_counts = [];
            if ($resident_counts_result->num_rows > 0) {
                while ($row = $resident_counts_result->fetch_assoc()) {
                    $resident_counts[$row['Status']] = $row['count'];
                }
            }
        } else {
            echo "<p>Error fetching resident counts: " . $conn->error . "</p>";
        }

        // Fetch landlord counts
        $landlord_counts_sql = "SELECT Citizen_category, COUNT(*) as count FROM resident GROUP BY Citizen_category";
        $landlord_counts_result = $conn->query($landlord_counts_sql);

        if ($landlord_counts_result) {
            $landlord_counts = [];
            if ($landlord_counts_result->num_rows > 0) {
                while ($row = $landlord_counts_result->fetch_assoc()) {
                    $landlord_counts[$row['Citizen_category']] = $row['count'];
                }
            }
        } else {
            echo "<p>Error fetching landlord counts: " . $conn->error . "</p>";
        }

        // Fetch user role counts
        $user_counts_sql = "SELECT Status, COUNT(*) as count FROM users GROUP BY Status";
        $user_counts_result = $conn->query($user_counts_sql);

        if ($user_counts_result) {
            $user_counts = [];
            if ($user_counts_result->num_rows > 0) {
                while ($row = $user_counts_result->fetch_assoc()) {
                    $user_counts[$row['Status']] = $row['count'];
                }
            }
        } else {
            echo "<p>Error fetching user counts: " . $conn->error . "</p>";
        }

        // Fetch house counts
        $house_counts_sql = "SELECT Status, COUNT(*) as count FROM houses GROUP BY Status";
        $house_counts_result = $conn->query($house_counts_sql);

        if ($house_counts_result) {
            $house_counts = [];
            if ($house_counts_result->num_rows > 0) {
                while ($row = $house_counts_result->fetch_assoc()) {
                    $house_counts[$row['Status']] = $row['count'];
                }
            }
        } else {
            echo "<p>Error fetching house counts: " . $conn->error . "</p>";
        }

        // Set default counts to 0 if not present
        $pending_count = isset($resident_counts['Pending']) ? $resident_counts['Pending'] : 0;
        $deleted_count = isset($resident_counts['Deleted']) ? $resident_counts['Deleted'] : 0;
        $available_count = isset($resident_counts['Available']) ? $resident_counts['Available'] : 0;
        $abroad_count = isset($resident_counts['Abroad']) ? $resident_counts['Abroad'] : 0;

        $inactive_count = isset($user_counts['2']) ? $user_counts['2'] : 0;
        $active_count = isset($user_counts['1']) ? $user_counts['1'] : 0;

        $normal_count = isset($landlord_counts['Normal']) ? $landlord_counts['Normal'] : 0;
        $landlord_count = isset($landlord_counts['Landlord']) ? $landlord_counts['Landlord'] : 0;

        $availablehouse_count = isset($house_counts['Available']) ? $house_counts['Available'] : 0;
        $destroyedhouse_count = isset($house_counts['Destroyed']) ? $house_counts['Destroyed'] : 0;

        // Dashboard data display
        echo "<div class='status'>
                <div><strong>No of available Residents in cell:</strong><br><h1>$available_count</h1></div>
                <div><strong>No of pending Residents:</strong><br><h1>$pending_count</h1></div>
                <div><strong>No of Deleted Residents in cells:</strong><br><h1>$deleted_count</h1></div>
              </div>";

        echo "<div class='status'>
                <div><strong>Citizens who are abroad:</strong><br><h1>$abroad_count</h1></div>
                <div><strong>Inactive Users:</strong><br><h1>$inactive_count</h1></div>
                <div><strong>Active Users:</strong><br><h1>$active_count</h1></div>
              </div>";

        echo "<div class='status'>
                <div><strong>Landlord/Landladies:</strong><br><h1>$landlord_count</h1></div>
                <div><strong>No of Residents who are not landlord:</strong><br><h1>$normal_count</h1></div>
              </div>";

        echo "<div class='status'>
                <div><strong>No of Houses:</strong><br><h1>$availablehouse_count</h1></div>
                <div><strong>Destroyed Houses:</strong><br><h1>$destroyedhouse_count</h1></div>
              </div>";
        ?>
        </div>
    </div>
    <!-- Footer -->
    <div class="footer">
        <h6>&copy; <b> 2024 All rights reserved to Raban</b></h6>
    </div>


    <?php
     }elseif($userRole == "Citizen"){

     }elseif($userRole == "RIB Officer"){
     require_once "dashoboard2.php";
     }else{
        ?>
        <h1>Unauthorized access</h1>
        <?php
     }
  ?>

</body>

</html>