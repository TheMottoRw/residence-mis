
<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Roles</title>
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

    // Prepare the SQL query to include the search condition and order by Identifier descending
    $sql = "SELECT RoleID, RoleName 
            FROM roles 
            WHERE CONCAT_WS(' ', RoleName) LIKE ? 
            ORDER BY RoleID DESC
            LIMIT ? OFFSET ?";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $search_param = "%$search_query%";
    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Query to get the total number of records
    $total_sql = "SELECT COUNT(*) as total FROM roles WHERE CONCAT_WS(' ', RoleName) LIKE ?";
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
                            <a class='small' href='addUserrole.php'>
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
                            <h2 class='text-center'>List Of User Roles</h2>
                        </div>
                    </div>
                </th>
              </tr>";
        // Column headers
        echo "<tr>
                <th align='Center' scope='col'>RoleID</th>
                <th align='Center' scope='col'>RoleName</th>
                <th align='Center' scope='col'>Action</th>
              </tr>";
        echo "</thead>";
        
        // Start the table body
        echo "<tbody>";
        
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <th align='Center'>" . htmlspecialchars($row["RoleID"]) . "</th>
                    <td align='Center'>" . htmlspecialchars($row["RoleName"]) . "</td>
                    <td align='Center'><a href='updateUserRole.php?roleid=" . htmlspecialchars($row["RoleID"]) . "' style='color: red;'><img src='images/edit.jpg' width='50px' height='50px'></a>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href='deleteRole.php?roleid=" . htmlspecialchars($row["RoleID"]) . "' style='color: red;'>Delete</a></td>
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
                            <a class='small' href='addRole.php'>
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
                            <h2 class='text-center'>List Of Roles in the system</h2>
                        </div>
                    </div>
                </th>
              </tr>";
        // Column headers
        echo "<tr>
                <th scope='col'>RoleID</th>
                <th scope='col'>RoleName</th>
                <th scope='col'>Action</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        
            echo "<tr><center>
                  <table>
                  <tr><td margin='center'>
                  <font color='Red' align='center' size='300px'><center><b>
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
</body>
</html>
