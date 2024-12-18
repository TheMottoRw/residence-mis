<?php
include 'connect.php';
include_once "includes/session_manager.php";
include_once "helper/MailUtils.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate requests</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php
include 'header.php';
?>


<?php
// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function filterQueryBuilder()
{
    global $conn;
    if ($_SESSION['role'] == 'Village_Leader') {
        $sql = "SELECT r.Identifier,r.Firstname,r.Lastname,r.Telephone,CONCAT(howner.Firstname,' ',howner.Lastname) as Landlord,howner.ID as LandlordId,howner.Telephone as LandlordPhone FROM resident r INNER JOIN houses h ON h.HouseNo=r.HouseNo INNER JOIN resident howner ON howner.ID=h.ID
WHERE CONCAT_WS(r.Identifier, r.ID) LIKE ? AND h.Cell='" . $_SESSION['cell'] . "' AND h.Village='" . $_SESSION['village'] . "'
 ORDER BY r.Identifier DESC LIMIT ? OFFSET ?";
    } else if ($_SESSION['role'] == 'Cell_Leader') {
        $sql = "SELECT r.Identifier,r.Firstname,r.Lastname,r.Telephone,CONCAT(howner.Firstname,' ',howner.Lastname) as Landlord,howner.ID as LandlordId,howner.Telephone as LandlordPhone FROM resident r INNER JOIN houses h ON h.HouseNo=r.HouseNo INNER JOIN resident howner ON howner.ID=h.ID
WHERE CONCAT_WS(r.Identifier, r.ID) LIKE ? AND h.Cell='" . $_SESSION['cell'] . "'
 ORDER BY r.Identifier DESC LIMIT ? OFFSET ?";
    }
    $st = $conn->prepare($sql);
    return $st;
}

function approveCertificateRequest($requestNo)
{
    global $conn;
    $date = date("Y-m-d H:i");
    if ($_SESSION['role'] == 'Landlord') {
        $sql = "UPDATE certificate_requests SET HouseOwnerApproval='1',HouseOwnerApprovedAt='".$date."' WHERE RequestNo=?";
    } else if ($_SESSION['role'] == 'Village_Leader') {
        $sql = "UPDATE certificate_requests SET VillageLeaderApproval='1',VillageLeaderApprovedAt='".$date."' WHERE RequestNo=?";
    } else if ($_SESSION['role'] == 'Cell_Leader') {
        $sql = "UPDATE certificate_requests SET CellLeaderApproval='1',CellLeaderApprovedAt='".$date."' WHERE RequestNo=?";
    }
    $st = $conn->prepare($sql);
    $st->bind_param("s", $requestNo);

    if ($st->execute()) {
        $stmt = $conn->prepare("SELECT r.Lastname,r.Telephone,r.Email FROM certificate_requests cr INNER JOIN resident r ON r.ID=cr.ID WHERE RequestNo=? AND HouseOwnerApproval='1' AND VillageLeaderApproval='1' AND CellLeaderApproval='1'");
        $stmt->bind_param("i",$requestNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($data){
            sendRequest(array("to"=>$data['Telephone']."@yopmail.com","subject"=>"Certificate request approved","body"=>"Dear ".$data['Lastname'].",<br>We are glad to inform you that your certificate request has been <b><font color='green'>APPROVED</font></b><br>Best Regards,<br>CRMS"));
            $stmt = $conn->prepare("UPDATE certificate_requests SET status='Approved',RejectionReason='' WHERE RequestNo=?");
            $stmt->bind_param("i",$requestNo);
            $stmt->execute();
        }
        echo "<div class='alert alert-success'><center>Request approved successful</center>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Can't approve request</div>";
    }
}


// Number of records to display per page
$records_per_page = 5;

// Get the current page number from query string, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Get the search query from query string, default is empty
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Prepare the SQL query to include the search condition and order by HouseNo descending
// Prepare the SQL query with the placeholders
$sql = "SELECT r.Firstname,r.Lastname,r.Telephone,CONCAT(howner.Firstname,' ',howner.Lastname) as Landlord,howner.ID as LandlordId,howner.Telephone as LandlordPhone FROM resident r WHERE r.Village = ?
AND CONCAT_WS(r.Identifier,r.ID) LIKE ? 
ORDER BY cr.RequestNo DESC
LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = filterQueryBuilder();

// Check if the statement preparation was successful
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

// Prepare the search parameter
$search_param = "%$search_query%";  // The search query for LIKE condition

// Bind the parameters: s for string (search_param), i for integer (LIMIT and OFFSET)
$stmt->bind_param("sii", $search_param, $records_per_page, $offset);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Query to get the total number of records
$total_sql = "SELECT COUNT(*) as total FROM resident WHERE CONCAT_WS(Identifier, ID) LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s", $search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Check if there are results
if ($result->num_rows > 0) {
    // Start the container for scrollable table
    echo "<div style='overflow-x: auto;'>";
    if(isset($_GET['RequestNo'])){
        approveCertificateRequest($_GET['RequestNo']);
    }
    if(isset($_GET['message'])) {
        echo "<div class='alert alert-success'><center>".$_GET['message']."</center>.</div>";
    }
    // Start the table
    echo "<table border='1' class='table table-striped' style='min-width: 100%;'>";

    // Define the table header with title row and button
    echo "<thead>";
    // Title and button row
    echo "<tr>
                <th colspan='16' style='text-align: left; background-color: #f8f9fa;'>
                    <div style='display: flex; align-items: center;'>
                        <div style='margin-right: 10px;'>
                            <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                                <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                                <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                            </form>
                        </div>
                        <div style='flex: 1; text-align: center; font-size: 24px;'>
                            <h2 class='text-center'>Certificate requests</h2>
                        </div>
                    </div>
                </th>
              </tr>";
    // Column headers
    echo "<tr>
                <th scope='col'>ResidentNo</th>
                <th scope='col'>Resident</th>
                <th scope='col'>Landlord</th>
                <th scope='col'>Category</th>
              </tr>";
    echo "</thead>";

    // Start the table body
    echo "<tbody>";

    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                    <td>" . htmlspecialchars($row["Identifier"]) . "</td>
                    <td>" . htmlspecialchars($row["ID"]) . "<br>" . $row['Firstname'] . " " . $row['Lastname'] . "<br>" . $row['Telephone'] . "</td>
                    <td>" . htmlspecialchars($row["Landlord"]) . "<br>" . $row['LandlordPhone'] . "</td>
                    <td>" . htmlspecialchars($row["Citizen_Category"]) . "</td>
                  </tr>";
    }

    // Close the table body
    echo "</tbody>";

    // Close the table
    echo "</table>";

    // Close the scrollable container
    echo "</div>";

    // Pagination controls
    echo "<div class='pagination'>";

    // Previous button
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search_query) . "' class='btn btn-primary' style='background-color: #007bff; border-color: #007bff;'>Previous</a>";
    } else {
        echo " &nbsp  &nbsp ";
        echo "<span class='btn btn-primary disabled'>Previous</span>";
        echo " &nbsp  &nbsp ";
    }

    // Page number links
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo " &nbsp  &nbsp ";
            echo "<span class='current'>$i</span>";
            echo " &nbsp  &nbsp ";
        } else {
            echo " &nbsp  &nbsp ";
            echo "<a href='?page=$i&search=" . urlencode($search_query) . "'>$i</a>";
            echo " &nbsp  &nbsp ";
        }
    }

    // Next button
    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search_query) . "' class='btn btn-primary' style='background-color: #007bff; border-color: #007bff;'>Next</a>";
    } else {
        echo " &nbsp  &nbsp ";
        echo "<span class='btn btn-primary disabled'>Next</span>";
    }

    echo "</div>";
} else {
    echo "<div style='overflow-x: auto;'>";
    // Start the table
    echo "<table border='1' class='table table-striped' style='min-width: 100%;'>";

    // Define the table header with title row and button
    echo "<thead>";
    // Title and button row
    echo "<tr>
                <th colspan='16' style='text-align: left; background-color: #f8f9fa;'>
                    <div style='display: flex; align-items: center;'>
                        <div style='margin-right: 10px;'>
                            <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                                <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                                <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                            </form>
                        </div>
                        <div style='flex: 1; text-align: center; font-size: 24px;'>
                            <h2 class='text-center'>List of certificate requests</h2>
                        </div>
                    </div>
                </th>
              </tr>";
    // Column headers
    echo "<tr>
                <th scope='col'>RequestNo</th>
                <th scope='col'>ResidentNo</th>
                <th scope='col'>ID</th>
                <th scope='col'>RequestDate</th>
                <th scope='col'>Owner approved</th>
                <th scope='col'>Village approved</th>
                <th scope='col'>Cell approved</th>
                <th scope='col'>Action</th>
              </tr>";
    echo "</thead>";
    echo "<tbody>";

    echo "<tr><center>
                  <table>
                  <tr><td margin='center'>
                  <font color='Red' align='center' size='500px'><center><b>
                  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
                  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 results<br>
                  </b></center></td>
                  </tr>
                  </table></center>
                  
                  </tr>";

    // Close the table body
    echo "</tbody>";

    // Close the table
    echo "</table>";

    // Close the scrollable container
    echo "</div>";

    // Pagination controls
    echo "<div class='pagination'>";
    // Previous button
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search_query) . "' class='btn btn-primary' style='background-color: #007bff; border-color: #007bff;'>Previous</a>";
    } else {
        echo "<span class='btn btn-primary disabled'>Previous</span>";
        echo " &nbsp  &nbsp ";
    }

    // Page number links
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo "<span class='current'>$i</span>";
        } else {
            echo "<a href='?page=$i&search=" . urlencode($search_query) . "'>$i</a>";
        }
    }

    // Next button
    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search_query) . "' class='btn btn-primary' style='background-color: #007bff; border-color: #007bff;'>Next</a>";
    } else {
        echo " &nbsp  &nbsp ";
        echo "<span class='btn btn-primary disabled'>Next</span>";
    }

    echo "</div>";

}

// Close connection
$conn->close();
?>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
<script>
    function rejectRequest(obj){
        console.log(obj.getAttribute("req-no"));
        let reason = prompt("Enter reason for rejection");
        if(reason.length>5){
            window.location="helper/api.php?find=rejectRequest&RequestNo="+obj.getAttribute("req-no")+"&reason="+reason;
        }
    }
</script>
</body>
</html>
