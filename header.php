<!-- Header -->
<?php
include_once "connect.php";
include_once "includes/session_manager.php";
session_controller();
?>
<div class="row" style="background:skyblue; height:50px; width:2000px; text-align: center; .header h3:hover {
            color: #ffdd57;">
    <div class="pageheader" style="text-align:center;">
        <h3>&nbsp &nbsp &nbsp &nbsp&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
        &nbsp &nbsp &nbsp &nbsp&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
        Citizen's Residence Management System (CRMS)</h3>
    </div>
</div>
<!-- End Header -->

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <!--<a class="navbar-brand" href="#">
        <img src="images/logo.jpg" width="20" height="20" alt="CRMS Logo">
    </a>-->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <?php
        if($_SESSION['role']=='admin'){?>
            <ul class="navbar-nav mr-auto">
                <!-- <li class="nav-item active">
                    <a class="nav-link" href="Homepage.php">Home <span class="sr-only">(current)</span></a>
                </li>-->
                <li>
                    <img src="images/logo.jpg" width="50" height="50" alt="CRMS Logo">
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="Dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item dropdown active">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                        <b>Registration</b>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="addResident.php">Citizen</a>
                        <a class="dropdown-item" href="addStatus.php">Resident_Status</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="addHouses.php">House</a>
                        <a class="dropdown-item" href="addResident.php">Landlord/Landlady</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="addUser.php">Users</a>
                        <a class="dropdown-item" href="addUserrole.php">Role</a>
                    </div>
                </li>
                <li class="nav-item active">
                    <a class="nav-link enabled" href="certificate.php">Certificate</a>
                </li>
                <li class="nav-item dropdown active">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                        <b>Reports</b>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="ListOfCitizens.php">Citizens</a>
                        <a class="dropdown-item" href="Resident.php">Residents</a>
                        <a class="dropdown-item" href="Status.php">Resident_Status</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="houses.php">House</a>
                        <a class="dropdown-item" href="Landlord.php">Landlord/Landlady</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="Users.php">Users</a>
                        <a class="dropdown-item" href="Role.php">Role</a>
                    </div>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>

            <?php
        }else{?>

        <ul class="navbar-nav mr-auto">
            <!-- <li class="nav-item active">
                <a class="nav-link" href="Homepage.php">Home <span class="sr-only">(current)</span></a>
            </li>-->
            <li>
                <img src="images/logo.jpg" width="50" height="50" alt="CRMS Logo">
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="CertificateRequestsView.php">Certificate Requests</a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="login.php">Logout</a>
            </li>
        </ul>
            <?php
        }
        ?>
    </div>
</nav>
<!-- End Navigation Bar -->
<?php

//session_start(); // Start the session

// Check if the user is logged in, if not, redirect to login page
//if (!isset($_SESSION['userID'])) 
//{
   // header("Location: login.php"); // Redirect to the login page
    //exit();
//}
?>