<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    
    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Additional CSS for the centered box -->
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Open Sans', sans-serif;
        }

        .centered-box {
            display: flex;
            justify-content: center;
            align-items: center;
            /* min-height: 100vh; */
            height: 700px;
        }

        .box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top header-inner-pages">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="Admin_Home.php">Syŕene</a></h1>
    
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="Admin_Home.php">Home</a></li>
                    <li class="dropdown"><a class="nav-link scrollto"><span>All</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="Song_List.php">Songs</a></li>
                            <li><a href="Artist_List.php">Artists</a></li>
                            <li><a href="Album_List.php">Albums</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a class="nav-link scrollto"><span>Add</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="adding_song.php">Song</a></li>
                            <li><a href="adding_artist.php">Artist</a></li>
                            <li><a href="adding_album.php">Album</a></li>
                        </ul>
                    </li>
                    <li><a class="nav-link scrollto" href="feedback.php">Feedback</a></li>
                    <li><a class="nav-link scrollto active" href="revenue.php">Revenue</a></li>
                    <li><a class="nav-link scrollto" href="logout.php">Logout</a></li> 
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
        </div>
    </header><!-- End Header -->

    <!-- ======= Main Section ======= -->
    <section id="main">
        <div class="container">
            <div class="centered-box">
                <div class="box">
                    <h2>Revenue and Subscriber Information</h2>
                    <?php
                    // PHP code for fetching and displaying data
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "newserene";

                    // Establishing connection
                    $conn = new mysqli($servername, $username, $password, $database);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query to get subscriber counts
                    $totalSubscribersQuery = "SELECT COUNT(*) as totalSubscribers FROM sereneuser";
                    $yearlySubscribersQuery = "SELECT COUNT(*) as yearlySubscribers FROM sereneuser WHERE subplan = 2";
                    $monthlySubscribersQuery = "SELECT COUNT(*) as monthlySubscribers FROM sereneuser WHERE subplan = 1";

                    // Execute queries
                    $totalResult = $conn->query($totalSubscribersQuery);
                    $yearlyResult = $conn->query($yearlySubscribersQuery);
                    $monthlyResult = $conn->query($monthlySubscribersQuery);

                    // Initialize variables
                    $totalSubscribers = 0;
                    $yearlySubscribers = 0;
                    $monthlySubscribers = 0;

                    // Process results
                    if ($totalResult && $yearlyResult && $monthlyResult) {
                        $totalSubscribers = $totalResult->fetch_assoc()['totalSubscribers'];
                        $yearlySubscribers = $yearlyResult->fetch_assoc()['yearlySubscribers'];
                        $monthlySubscribers = $monthlyResult->fetch_assoc()['monthlySubscribers'];
                    } else {
                        echo "Error retrieving data: " . $conn->error;
                    }

                    // Calculate revenue
                    $yearlyRevenue = $yearlySubscribers * 100;
                    $monthlyRevenue = $monthlySubscribers * 15;
                    $totalRevenue = $yearlyRevenue + $monthlyRevenue;

                    // Output the results
                    echo "<p>Total Subscribers: " . ($yearlySubscribers + $monthlySubscribers) . "</p>";
                    echo "<p>Yearly Subscribers: $yearlySubscribers</p>";
                    echo "<p>Monthly Subscribers: $monthlySubscribers</p>";
                    echo "<p>Total Revenue: $" . $totalRevenue . "</p>";

                    // Close the connection
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </section><!-- End Main Section -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>
</html>
