<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All_Citizens</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Compact table style */
        table {
            align: Center;
            table-layout: fixed;
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

// Prepare the SQL query to include the search condition and order by Identifier descending
$sql = "SELECT Identifier, Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status,RegDate 
        FROM resident 
        WHERE CONCAT_WS(' ', Firstname, Lastname, DoB, Telephone, Gender, ID, FatherNames, MotherNames, Province, District, Sector, Cell, Village, citizen_category, HouseNo, Status,RegDate) LIKE ? 
        ORDER BY Identifier DESC
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
    echo "<table border='1' class='table table-striped' style='min-width: 60%;>";
    
    // Define the table header with title row and button
    echo "<thead>";
    // Title and button row
    echo "<tr>
            <th colspan='9' style='text-align: left; background-color: #f8f9fa;'>
                <div style='display: flex; align-items: center;'>
                    <div style='margin-right: 10px;'>
                        <a class='small' href='addResident.php'>
                            <button class='btn btn-primary'><b>Add New+</b></button>
                        </a>
                    </div>
                    <div style='margin-right: 8px;'>
                        <form class='form-inline my-2 my-lg-0' method='GET' action=''>
                            <input class='form-control mr-sm-2' type='search' name='search' placeholder='Search' aria-label='Search' value='" . htmlspecialchars($search_query) . "'>
                            <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
                        </form>
                    </div>
                    <div style='flex: 1; text-align: center; font-size: 18px;'>
                        <h2 class='text-center'>List Of All Citizens</h2>
                    </div>
                     <div style='margin-right: 10px;'>
                        <a class='small' href='ReportGeneration.php'>
                            <button class='btn btn-primary'><b>GenerateReport</b></button>
                        </a>
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
            <th scope='col'>Action</th>
          </tr>";
    echo "</thead>";
    
    // Start the table body
    echo "<tbody>";
    
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <th>" . htmlspecialchars($row["Identifier"]) . "</th>
                <td>" . htmlspecialchars($row["Firstname"]) . "</td>
                <td>" . htmlspecialchars($row["Lastname"]) . "</td>
                <td>" . htmlspecialchars($row["DoB"]) . "</td>
                <td>" . htmlspecialchars($row["Telephone"]) . "</td>
                <td>" . htmlspecialchars($row["Gender"]) . "</td>
                <td>" . htmlspecialchars($row["ID"]) . "</td>
                <td>" . htmlspecialchars($row["FatherNames"]) . "</td>
                <td>" . htmlspecialchars($row["MotherNames"]) . "</td>
                <td><a href='updateResident.php?identifier=" . htmlspecialchars($row["Identifier"]) . "' style='color: red;'>Assign a House</a> &nbsp&nbsp||&nbsp&nbsp <a href='updateCitizen.php?identifier=" . htmlspecialchars($row["Identifier"]) . "' style='color: red;'><img src='images/edit.jpg' width='30px' height='30px'></a></td>
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
    echo "<thead><tr><th colspan='9'><center><font color='Red'>0</font></center></th></tr></thead>";
    echo "<tbody><tr><td colspan='9' style='text-align: center;'><font color='Red'>No results found for the search query</font></td></tr></tbody>";
    echo "</table></div>";
}

// Close connection
$conn->close();
?>
<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>