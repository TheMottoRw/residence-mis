<?php include 'header.php'; ?>
<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-image: url('images/background.jpg'); /* Update path to background image */
            background-size: cover;
            background-attachment: fixed; /* Keeps background fixed */
            background-position: center;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1; /* Makes sure the content area grows to take available space */
        }
        .carousel-item img {
            height: 100vh; /* Full height for carousel items */
            object-fit: cover; /* Ensures images cover the container without distortion */
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5); /* Darker control icons */
        }
        .dashboard {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white background */
            padding: 20px;
            margin: 20px auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px; /* Restrict maximum width */
        }
        .dashboard h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard .status {
            display: flex;
            justify-content: space-around;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #ffffff;
        }
        .dashboard .status div {
            flex: 1;
            text-align: center;
        }
        .dashboard .status div span {
            font-size: 1.5em;
            font-weight: bold;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
            border-radius: 3px;
        }
        .pagination a.disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }
        .pagination span.current {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .footer {
            background: skyblue;
            height: 25px;
            text-align: center;
            line-height: 25px; /* Vertically center text */
            color: #fff;
            width: 100%;
            position: relative; /* Ensure it stays in flow */
        }
    </style>
</head>
<body>

    <!--
    <div class="content">
        
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/image1.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/image2.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/image3.jpg" class="d-block w-100" alt="...">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div> -->

        <!-- Dashboard -->
        <div class="dashboard">
            <h3>Dashboard</h3>
            <?php
            // Fetch status counts
            $status_counts_sql = "SELECT Status, COUNT(*) as count FROM resident GROUP BY Status";
            $status_counts_result = $conn->query($status_counts_sql);

            $status_counts = [];
            if ($status_counts_result->num_rows > 0) {
                while ($row = $status_counts_result->fetch_assoc()) {
                    $status_counts[$row['Status']] = $row['count'];
                }
            }

            // Set default counts to 0 if not present
            $pending_count = isset($status_counts['PENDING']) ? $status_counts['PENDING'] : 0;
            $deleted_count = isset($status_counts['DELETED']) ? $status_counts['DELETED'] : 0;
            $available_count = isset($status_counts['AVAILABLE']) ? $status_counts['AVAILABLE'] : 0;

            // Display the dashboard
            echo "<div class='status'>
                    <div>
                        <strong>Available:</strong><br>
                        <span>$available_count</span>
                    </div>
                    <div>
                        <strong>Pending:</strong><br>
                        <span>$pending_count</span>
                    </div>
                    <div>
                        <strong>Deleted:</strong><br>
                        <span>$deleted_count</span>
                    </div>
                  </div>";
            ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <h6>&copy; All rights reserved to Raban 2024</h6>
    </div>

    <script src="bootstrap/jquery.slim.js"></script>
    <script src="bootstrap/bootstrap.bundle.js"></script>
</body>
</html>