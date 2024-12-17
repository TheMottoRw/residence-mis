<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People in jail</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="jsPDF/jspdf.plugin.autotable.min.js">
    <link rel="stylesheet" href="jsPDF/jspdf.umd.min.js">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.26/jspdf.plugin.autotable.min.js"></script> <!-- Add autoTable plugin -->
    <style>
        /* Compact table style */
        table {
            table-layout: auto;
            width: 100%;
            font-size: 12px; /* Reduce the font size */
        }
        th, td {
            padding: 5px 8px; /* Decrease padding */
            word-wrap: break-word;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        td {
            background-color: #ffffff;
        }
        .pagination a, .pagination span {
            font-size: 12px; /* Reduce pagination font size */
            padding: 6px 12px;
        }
        .pagination {
            margin-top: 15px;
        }
        .table-container {
            overflow-x: auto;
        }
        .pagination a {
            text-decoration: none;
        }
        .pagination .current {
            font-weight: bold;
        }
    </style>

</head>
<body>
<?php include 'header.php'; ?>


<?php
// Database connection
$conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Number of records to display per page
$records_per_page = 7;

// Get the current page number from query string, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Get the search query from query string, default is empty
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Prepare the SQL query to include the search condition and order by HouseNo descending
// Prepare the SQL query with the placeholders
if ($_SESSION['role'] == 'Prison')
{
    $sql = "SELECT h.HouseNo, p.Province, d.District, s.Sector, c.Cell, v.Village,h.ID,CONCAT(t.Firstname,' ',t.Lastname) as tenant_name,t.Telephone as tenant_phone,t.ID as tenant_id
        FROM houses h INNER JOIN provinces p ON h.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=h.District INNER JOIN sectors s ON s.SectorID=h.Sector 
                      INNER JOIN cells c ON c.CellID=h.Cell LEFT JOIN villages v ON v.VillageID=h.Village INNER JOIN resident t ON t.HouseNo=h.HouseNo AND t.ID!='".$_SESSION['ID']."'
        WHERE CONCAT_WS( h.HouseNo, p.Province, d.District, s.Sector, c.Cell, v.Village,h.ID) LIKE ?  AND h.ID='" . $_SESSION['ID'] . "'
        ORDER BY h.HouseNo DESC
        LIMIT ? OFFSET ?";

}

$stmt = $conn->prepare("SELECT j.*,r.* FROM jailed j INNER JOIN resident r ON r.ID=j.ResidentID WHERE j.JailedBy=? AND CONCAT_WS( r.ID, r.Firstname, r.Lastname) LIKE ? ");

// Prepare the statement
//$stmt = $conn->prepare($sql);

// Check if the statement preparation was successful
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

// Prepare the search parameter
$search_param = "%$search_query%";  // The search query for LIKE condition

// Bind the parameters: s for string (search_param), i for integer (LIMIT and OFFSET)
$stmt->bind_param("is", $_SESSION['ID'],$search_param);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Query to get the total number of records
$total_sql = "SELECT COUNT(*) as total FROM jailed j INNER JOIN resident r ON r.ID=j.ResidentID WHERE j.JailedBy=? AND CONCAT_WS( r.ID, r.Firstname, r.Lastname) LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("ss", $_SESSION['ID'],$search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Check if there are results
if ($result->num_rows > 0) {
    // Start the container for scrollable table
    echo "<div style='overflow-x: auto;'>";
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
                            <a class='small' href='addJailed.php'>
                                <button class='btn btn-primary'><b>Add New+</b></button>
                            </a>
                        </div>
                        <div style='margin-right: 10px;'>
                            <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                                <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                                <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                            </form>
                        </div>
                        <div style='flex: 1; text-align: center; font-size: 24px;'>
                            <h2 class='text-center'>List Of All tenants</h2>
                        </div>
                        <div style='margin-right: 10px;'>
                            <button class='btn btn-primary' id='generateReportButton'><b>DownloadReport</b></button>
                     </div>
                    </div>
                </th>
              </tr>";
    // Column headers
    echo "<tr>
                <th scope='col'>HouseNo</th>
                <th scope='col'>Prisoner Name</th>
                <th scope='col'>Phone</th>
                <th scope='col'>Prisoner ID</th>
                <th scope='col'>Reason</th>
                <th scope='col'>Action</th>
              </tr>";
    echo "</thead>";

    // Start the table body
    echo "<tbody>";

    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                    <td>" . htmlspecialchars($row["HouseNo"]) . "</td>
                    <td>" . htmlspecialchars($row["Firstname"]) . " " . htmlspecialchars($row["Lastname"]) . " </td>
                    <td>" . htmlspecialchars($row["Telephone"]) . "</td>
                    <td>" . htmlspecialchars($row["ID"]) . "</td>
                    <td>" . htmlspecialchars($row["reason"]) . "</td>
                    <td>".($row["status"]=='Inprison'?"<a href='helper/api.php?find=removeJailed&ResidentID=".$row["ResidentID"]."&id=".$row["id"]."' style='color: red;'>Release prisoner</a>":'Released')."</td>
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
                            <a class='small' href='addHouse.php'>
                                <button class='btn btn-primary'><b>Add New+</b></button>
                            </a>
                        </div>
                        <div style='margin-right: 10px;'>
                            <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                                <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                                <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                            </form>
                        </div>
                        <div style='flex: 1; text-align: center; font-size: 24px;'>
                            <h2 class='text-center'>List Of All Houses</h2>
                        </div>
                    </div>
                </th>
              </tr>";
    // Column headers
    echo "<tr>
                <th scope='col'>HouseNo</th>
                <th scope='col'>Province</th>
                <th scope='col'>District</th>
                <th scope='col'>Sector</th>
                <th scope='col'>Cell</th>
                <th scope='col'>Village</th>
                <th scope='col'>OwnerID</th>
                <th scope='col'>Details</th>
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
    document.getElementById('generateReportButton').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');  // Landscape mode

        // Set title
        doc.setFontSize(18);
        doc.text('Registered House Report', 210, 10, null, null, 'center');
        doc.setFontSize(10);
        doc.text('Generated on: ' + new Date().toLocaleString(), 210, 20, null, null, 'center');

        // Define table headers
        const headers = ['HouseNo', 'Province', 'District', 'Sector', 'Cell', 'Village', 'OwnerID'];

        // Collect table data
        let tableData = [];
        document.querySelectorAll('table tbody tr').forEach(row => {
            let rowData = [];
            row.querySelectorAll('td').forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            tableData.push(rowData);
        });

        // Use autoTable to add data to PDF
        doc.autoTable({
            head: [headers], // Set the table headers
            body: tableData, // Add the table data
            startY: 30,  // Start position of the table
            theme: 'striped',  // Optional styling
            margin: { top: 10, left: 10, right: 10, bottom: 10 }
        });

        // Save the PDF
        doc.save('House_Registered_Report.pdf');
    });
</script>

</body>
</html>
