<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Citizens</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Styling for the table */
        table {
            table-layout: fixed;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            padding: 5px 8px;
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
            font-size: 12px;
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

// Number of records per page
$records_per_page = 5;

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get the search query from the URL
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query to fetch data from database with search and pagination
$sql = "SELECT Identifier, Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status 
        FROM resident 
        WHERE CONCAT_WS(' ', Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status) LIKE ? 
        ORDER BY Identifier DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("sii", $search_param, $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Query to get total number of records
$total_sql = "SELECT COUNT(*) as total FROM resident WHERE CONCAT_WS(' ', Firstname, Lastname, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status) LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s", $search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Check if there are results
if ($result->num_rows > 0) {
    // Start the table container for scrollable table
    echo "<div class='table-container'>";

    // Start the table and header row
    echo "<table border='1' class='table table-striped'>";
    echo "<thead>";
    echo "<tr>
            <th colspan='18' style='background-color: #f8f9fa; text-align: left;'>
                <div style='display: flex; align-items: center;'>
                    <div style='margin-right: 10px;'>
                        <a class='small' href='addResident.php'>
                            <button class='btn btn-primary'><b>Add New+</b></button>
                        </a>
                    </div>
                    <div style='margin-right: 8px;'>
                        <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                            <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' value='" . htmlspecialchars($search_query) . "'>
                            <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                        </form>
                    </div>
                    <div style='flex: 1; text-align: center; font-size: 18px;'>
                        <h2 class='text-center'>List Of All Citizens</h2>
                    </div>
                </div>
            </th>
          </tr>";
    echo "<tr>
            <th>Resident No</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>Telephone</th>
            <th>Gender</th>
            <th>ID</th>
            <th>Father Names</th>
            <th>Mother Names</th>
            <th>Province</th>
            <th>District</th>
            <th>Sector</th>
            <th>Cell</th>
            <th>Village</th>
            <th>Citizen Category</th>
            <th>House No</th>
            <th>Status</th>
            <th>Action</th>
          </tr>";
    echo "</thead>";
    
    // Output data for each resident in the table
    echo "<tbody>";
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
                <td>" . htmlspecialchars($row["citizen_category"]) . "</td>
                <td>" . htmlspecialchars($row["HouseNo"]) . "</td>
                <td style='color: red;'>" . htmlspecialchars($row["Status"]) . "</td>
                <td><a href='updateResident.php?identifier=" . htmlspecialchars($row["Identifier"]) . "' style='color: red;'>
                    <img src='images/edit.jpg' width='50px' height='50px'></a>
                </td>
              </tr>";
    }
    echo "</tbody>";
    echo "</table>";
    
    // Pagination links
    echo "<div class='pagination'>";
    // Previous button
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search_query) . "'>Previous</a>";
    } else {
        echo "<span class='disabled'>Previous</span>";
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo "<span class='current'>$i</span>";
        } else {
            echo "<a href='?page=$i&search=" . urlencode($search_query) . "'>$i</a>";
        }
    }

    // Next button
    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search_query) . "'>Next</a>";
    } else {
        echo "<span class='disabled'>Next</span>";
    }
    echo "</div>";
} else {
    echo "<div>No results found for the search query.</div>";
}

$conn->close();
?>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
