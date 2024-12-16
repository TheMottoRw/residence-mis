<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen with no houses</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.26/jspdf.plugin.autotable.min.js"></script> <!-- Add autoTable plugin -->

    <style>
        /* Compact table style */
        table {
            table-layout: Auto;
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
$records_per_page = 5;

// Get the current page number from query string, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Get the search query from query string, default is empty
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Prepare the SQL query to include the search condition and order by Identifier descending    t  INNER JOIN provinces p ON t.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=t.District INNER JOIN sectors s ON s.SectorID=t.Sector INNER JOIN cells c ON c.CellID=t.Cell INNER JOIN villages v ON v.VillageID=t.Village
$sql = "SELECT t.Identifier, t.Firstname, t.Lastname, t.DoB, t.Telephone, t.Gender, t.ID, t.FatherNames, t.MotherNames, p.Province, d.District, s.Sector, c.Cell, v.Village, t.Citizen_Category, t.HouseNo, t.Status, t.RegDate 
        FROM resident t  INNER JOIN provinces p ON t.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=t.District INNER JOIN sectors s ON s.SectorID=t.Sector INNER JOIN cells c ON c.CellID=t.Cell INNER JOIN villages v ON v.VillageID=t.Village
        WHERE Status='Pending' AND HouseNo=''  
        AND CONCAT_WS(' ', t.Firstname, t.Lastname, t.DoB, t.Telephone, t.Gender, t.ID, t.FatherNames, t.MotherNames, p.Province, d.District, s.Sector, c.Cell, v.Village, t.Citizen_Category, t.HouseNo,t.Status, t.RegDate) LIKE ? 
        ORDER BY RegDate DESC 
        LIMIT ? OFFSET ?";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("sii", $search_param, $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Query to get the total number of records
$total_sql = "SELECT COUNT(*) as total FROM resident WHERE CONCAT_WS(' ', Firstname, Lastname, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status,RegDate) LIKE ?";
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
    echo "<div class='table-container'>";

    // Start the table
    echo "<table border='1' class='table table-striped' style='min-width: 60%; >";

    // Define the table header with title row and button
    echo "<thead>";
    // Title and button row
    echo "<tr>
            <th colspan='16' style='text-align: left; background-color: #f8f9fa;'>
                <div style='display: flex; align-items: center;'>
                    <div style='margin-right: 10px;'>
                        <a class='small' href='addTenant.php'>
                            <button class='btn btn-primary'><b>Add New+</b></button>
                        </a>
                    </div>
                    <div style='margin-right: 8px;'>
                        <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                            <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                            <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                        </form>
                    </div>
                    <div style='flex: 1; text-align: center; font-size: 24px;'>
                            <h2 class='text-center'>List of citizen with no Houses</h2>
                        </div>
                    <div style='margin-right: 10px;'>
                        <button class='btn btn-primary' id='generateReportButton'><b>DownloadReport</b></button>
                    </div>
                </div>
            </th>
          </tr>";
    // Column headers
    echo "<tr>
            <th scope='col'>Resident   N<u>o</u></th>
            <th scope='col'>FirstName</th>
            <th scope='col'>LastName</th>
            <th scope='col'>DoB</th>
            <th scope='col'>Telephone</th>
            <th scope='col'>Gender</th>
            <th scope='col'>ID</th>
            <th scope='col'>Father    Names</th>
            <th scope='col'>Mother    Names</th>
            <th scope='col'>Province</th>
            <th scope='col'>District</th>
            <th scope='col'>Sector</th>
            <th scope='col'>Cell</th>
            <th scope='col'>Village</th>
            <th scope='col'>HouseNo</th>
            <th scope='col'>Category</th>
            <th scope='col'>Status</th>
            <th scope='col'>Action</th>
          </tr>";
    echo "</thead>";

    // Start the table body
    echo "<tbody>";

    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["Identifier"]) . "</td>
                <td>" . htmlspecialchars($row["Firstname"]) . "</td>
                <td>" . htmlspecialchars($row["Lastname"]) . "</td>
                <td>" . htmlspecialchars($row["DoB"]) . "</td>
                <td>" . htmlspecialchars($row["Telephone"]) . "</td>
                <td>" . htmlspecialchars($row["Gender"]) . "</td>
                <td>" . htmlspecialchars($row["ID"]) . "</td>
                <td>" . htmlspecialchars($row["FatherNames"]) . "</td>
                <td>" . htmlspecialchars($row["MotherNames"]) . "</td>
                <td>" . htmlspecialchars($row["Province"]) . "</td>
                <td>" . htmlspecialchars($row["District"]) . "</td>
                <td>" . htmlspecialchars($row["Sector"]) . "</td>
                <td>" . htmlspecialchars($row["Cell"]) . "</td>
                <td>" . htmlspecialchars($row["Village"]) . "</td>
                <td>" . htmlspecialchars($row["HouseNo"]) . "</td>
                <td>" . htmlspecialchars($row["Citizen_Category"]) . "</td>
                <td style='color: red;'>" . htmlspecialchars($row["Status"]) . "</td>
                <td><a href='updateResident.php?identifier=" . htmlspecialchars($row["Identifier"]) . "' style='color: red;'><img src='images/edit.jpg' width='50px' height='50px'></a></td>
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
        echo "<span class='btn btn-primary disabled'>Previous</span>";
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
        echo "<span class='btn btn-primary disabled'>Next</span>";
    }

    echo "</div>";
} else {
    echo "<div class='table-container'>";
    echo "<table border='1' class='table table-striped'>";
    echo "<thead><tr><th colspan='16'><center><font color='Red'>0</font></center></th></tr></thead>";
    echo "<tbody><tr><td colspan='16' style='text-align: center;'><font color='Red'>No results found for the search query</font></td></tr></tbody>";
    echo "</table></div>";
}

// Close connection
$conn->close();
?>
<script>
    document.getElementById('generateReportButton').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');  // Landscape mode

        // Set title
        doc.setFontSize(18);
        doc.text('Resident Report', 210, 10, null, null, 'center');
        doc.setFontSize(8);
        doc.text('Generated on: ' + new Date().toLocaleString(), 210, 20, null, null, 'center');

        // Define table headers
        const headers = ['Resident No', 'FirstName', 'LastName', 'DoB', 'Telephone', 'Gender', 'ID', 'Father\'s Name', 'Mother\'s Name', 'Province', 'District', 'Sector', 'Cell', 'Village', 'HouseNo', 'Status'];

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
            head: [headers],
            body: tableData,
            startY: 30,  // Start position of the table
            theme: 'striped',  // Optional styling
            margin: { top: 10, left: 10, right: 10, bottom: 10 }
        });

        // Save the PDF
        doc.save('resident_report.pdf');
    });
</script>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
