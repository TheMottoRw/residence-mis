<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> User's List</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
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

// Prepare the SQL query to include the search condition and order by UserID descending
$sql = "SELECT u.UserID, u.Firstname, u.Lastname, u.Email, u.ID, u.Role, u.Telephone, u.ID, p.Province, d.District, s.Sector, c.Cell, v.Village, u.Status
        FROM users u  INNER JOIN provinces p ON u.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=u.District INNER JOIN sectors s ON s.SectorID=u.Sector INNER JOIN cells c ON c.CellID=u.Cell INNER JOIN villages v ON v.VillageID=u.Village
        WHERE CONCAT_WS(' ',  u.Firstname, u.Lastname, u.Email, u.ID, u.Role, u.Telephone, u.ID, p.Province, d.District, s.Sector, c.Cell, v.Village, u.Status) LIKE ? 
        ORDER BY u.UserID DESC
        LIMIT ? OFFSET ?";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("sii", $search_param, $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Query to get the total number of records
$total_sql = "SELECT COUNT(*) as total FROM users WHERE CONCAT_WS(' ',  Firstname, Lastname, Email, ID, Role, Telephone, ID, Province, District, Sector, Cell, Village, Status) LIKE ?";
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

    // Start the table
    echo "<table border='1' class='table table-striped' style='min-width: 100%;'>";

    // Define the table header with title row and button
    echo "<thead>";
    // Title and button row
    echo "<tr>
            <th colspan='16' style='text-align: left; background-color: #f8f9fa;'>
                <div style='display: flex; align-items: center;'>
                    <div style='margin-right: 10px;'>
                        <a class='small' href='addUser.php'>
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
                        <h2 class='text-center'>List Of Users</h2>
                    </div>
                </div>
            </th>
          </tr>";
    // Column headers
    echo "<tr>
            <th scope='col'>UserID</th>
            <th scope='col'>Firstname</th>
            <th scope='col'>Lastname</th>
            <th scope='col'>Email</th>
            <th scope='col'>Role</th>
            <th scope='col'>Telephone</th>
            <th scope='col'>ID</th>
            <th scope='col'>Province</th>
            <th scope='col'>District</th>
            <th scope='col'>Sector</th>
            <th scope='col'>Cell</th>
            <th scope='col'>Village</th>
            <th scope='col'>Status</th>
            <th scope='col'>Action</th>
          </tr>";
    echo "</thead>";

    // Start the table body
    echo "<tbody>";

    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        // Check the user's status and display Active/Inactive
        $status = ($row["Status"] == 1) ? "Active" : "Inactive";
        echo "<tr>
                <th>" . htmlspecialchars($row["UserID"]) . "</th>
                <td>" . htmlspecialchars($row["Firstname"]) . "</td>
                <td>" . htmlspecialchars($row["Lastname"]) . "</td>
                <td>" . htmlspecialchars($row["Email"]) . "</td>
                <td>" . htmlspecialchars($row["Role"]) . "</td>
                <td>" . htmlspecialchars($row["Telephone"]) . "</td>
                <td>" . htmlspecialchars($row["ID"]) . "</td>
                <td>" . htmlspecialchars($row["Province"]) . "</td>
                <td>" . htmlspecialchars($row["District"]) . "</td>
                <td>" . htmlspecialchars($row["Sector"]) . "</td>
                <td>" . htmlspecialchars($row["Cell"]) . "</td>
                <td>" . htmlspecialchars($row["Village"]) . "</td>
                <td style='color: red;'>" . $status . "</td>
                <td><a href='updateUser.php?userID=" . htmlspecialchars($row["UserID"]) . "' style='color: red;'><img src='images/edit.jpg' width='50px' height='50px'></a>
                <br><a href='deleteUser.php?userID=" . htmlspecialchars($row["UserID"]) . "' style='color: red;'>Delete</a></td>
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
    echo "<div style='overflow-x: auto;'>";
    echo "<table border='1' class='table table-striped' style='min-width: 100%;'>";
    echo "<thead><tr><th colspan='13'>No records found</th></tr></thead>";
    echo "<tbody><tr><td colspan='13'>No users to display.</td></tr></tbody>";
    echo "</table></div>";
}

// Close connection
$conn->close();
?>

<script src="bootstrap/jquery.slim.js"></script>
<script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
