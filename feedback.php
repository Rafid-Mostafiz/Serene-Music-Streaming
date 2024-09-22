<?php
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

// Handle mark as read functionality
if (isset($_POST['mark_read'])) {
    $message_ids = isset($_POST['message_id']) ? $_POST['message_id'] : [];
    if (!empty($message_ids)) {
        $message_ids_str = implode(',', $message_ids);
        $update_query = "UPDATE messages SET `read` = 1 WHERE id IN ($message_ids_str)";
        if (mysqli_query($conn, $update_query)) {
            // No message needed here
        } else {
            echo '<div class="alert alert-danger" role="alert">Error updating record: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// Determine if we need to filter by read or unread messages
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_query = '';
if ($filter == 'unread') {
    $filter_query = " AND `read` = 0";
} elseif ($filter == 'read') {
    $filter_query = " AND `read` = 1";
}

// Query to fetch data from the messages table sorted by created_at
$query = "SELECT id, name, email, subject, message, created_at, `read` FROM messages WHERE 1 $filter_query ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Check if query was successful
if ($result) {
    // Determine if we need to show the Save button
    $show_save_button = ($filter == 'all' || $filter == 'unread');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Feedback Messages</title>
        
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

        <style>
            /* Custom styles for feedback section */
            .feedback-section {
                padding: 80px 0;
            }
            .feedback-item {
                margin-bottom: 30px;
                border: 1px solid #ddd;
                padding: 20px;
                border-radius: 5px;
                width: 48%;
                display: inline-block;
                vertical-align: top;
                margin-right: 1%;
            }
            .feedback-item h3 {
                margin-bottom: 10px;
            }
            .filter-links {
                margin-bottom: 20px;
            }
            .filter-links a {
                color: #555;
                text-decoration: none;
                padding: 5px 10px;
                border-radius: 5px;
            }
            .filter-links a.active {
                background-color: #007bff;
                color: #fff;
            }
            .save-btn {
                margin-bottom: 20px;
                background-color: #dc3545;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
            }
            .save-btn:hover {
                background-color: #c82333;
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
                <li><a class="nav-link scrollto " href="Admin_Home.php">Home</a></li>
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
                <li><a class="nav-link scrollto active " href="feedback.php">Feedback</a></li>
                <li><a class="nav-link scrollto  " href="revenue.php">Revenue</a></li>
                <li><a class="nav-link scrollto " href="logout.php">Logout</a></li> 
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->
    </div>
</header><!-- End Header -->

        <!-- ======= Feedback Section ======= -->
        <section id="feedback" class="feedback-section">
            <div class="container">
                <h2>Feedback Messages</h2>

                <!-- Filter links -->
                <div class="filter-links">
                    <a href="feedback.php?filter=all" class="<?php echo ($filter == 'all') ? 'active' : ''; ?>">All</a> |
                    <a href="feedback.php?filter=unread" class="<?php echo ($filter == 'unread') ? 'active' : ''; ?>">Unread</a> |
                    <a href="feedback.php?filter=read" class="<?php echo ($filter == 'read') ? 'active' : ''; ?>">Read</a>
                </div>

                <form action="" method="POST">
                    <?php if (isset($show_save_button) && $show_save_button): ?>
                        <input type="submit" name="mark_read" value="Save" class="save-btn">
                    <?php endif; ?>
                    
                    <div class="feedback-list">
                        <?php
                        $count = 0;
                        // Loop through each row in the result set
                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($count % 2 == 0) {
                                echo '<div class="row">';
                            }
                            ?>
                            <div class="feedback-item col-md-5">
                                <h3><?php echo htmlspecialchars($row['subject']); ?></h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                                <p><strong>Message:</strong></p>
                                <p><?php echo htmlspecialchars($row['message']); ?></p>
                                <p><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                                
                                <!-- Checkbox to mark as read -->
                                <?php if ($row['read'] == 0): ?>
                                    <input type="checkbox" name="message_id[]" value="<?php echo $row['id']; ?>">
                                    <label for="message_id">Mark as Read</label><br>
                                <?php endif; ?>
                            </div>
                            <?php
                            $count++;
                            if ($count % 2 == 0) {
                                echo '</div>'; // Close row after two items
                            }
                        }

                        // Close row if there's an odd number of items
                        if ($count % 2 != 0) {
                            echo '</div>';
                        }
                        ?>
                    </div>
                </form>
            </div>
        </section><!-- End Feedback Section -->

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
    <?php
} else {
    echo '<div class="alert alert-danger" role="alert">Error fetching data: ' . mysqli_error($conn) . '</div>';
}

// Close the database connection
mysqli_close($conn);
?>
