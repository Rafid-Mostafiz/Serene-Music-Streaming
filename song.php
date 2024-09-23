<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$songID = null;

// Check if the song ID is passed through the query parameter
if (isset($_GET['song_id'])) {
    $songID = $_GET['song_id'];

    // Fetch the selected song
    $query = "SELECT s.*, a.ArtistName FROM song s JOIN artist a ON s.ArtistID = a.ArtistID WHERE s.SongID = $songID";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['Title'];
        $artistName = $row['ArtistName'];
        $duration = $row['Duration'];
        $releaseDate = $row['ReleaseDate'];
        $imageData = $row['ImageData'];
    } else {
        // Handle the case where the song ID is not valid
        die("Invalid song ID");
    }
} else {
    // Handle the case where no song ID is provided
    die("Song ID not specified");
}

// Check if the play button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['play'])) {
    // Increment the click count
    $updateQuery = "UPDATE song SET ClickCount = ClickCount + 1 WHERE SongID = $songID";
    $conn->query($updateQuery);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syŕene</title>

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
        /* Centered container */
        .container-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh; /* Adjusted min-height */
            padding: 20px; /* Added padding */
        }

        /* Styling for the play button */
        .play-button {
            background-color: black; /* Black background */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px; /* Adjusted margin */
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 10px;
        }

        .play-button:hover {
            background-color: white;
            color: black;
            border: 2px solid black; /* Black border on hover */
        }

        /* Box-like container */
        .song-container {
            background-color: #f8f9fa; /* Light gray background */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%; /* Set the width of the container */
            max-width: 600px; /* Set the maximum width of the container */
            text-align: center; /* Center align content */
            margin-bottom: 20px; /* Added margin bottom */
        }

        .song-container img {
            width: 100%; /* Make image responsive within container */
            max-height: 300px; /* Limit maximum height */
            object-fit: cover; /* Maintain aspect ratio */
            border-radius: 10px; /* Rounded corners */
            margin-bottom: 20px; /* Space below image */
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top header-inner-pages">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="Home_Page.php">Syŕene</a></h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="Home_Page.php">Home</a></li>
                    <li><a class="nav-link scrollto" href="Artists.php">Artists</a></li>
                    <li><a class="nav-link scrollto" href="Albums.php">Albums</a></li>
                    <li class="dropdown"><a class="nav-link scrollto"><span>Genre</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="genre.php">Pop</a></li>
                            <li><a href="#">Rock</a></li>
                            <li><a href="#">Hip-Hop</a></li>
                        </ul>
                    </li>
                    <li><a class="nav-link scrollto active" href="Songs.php">Songs</a></li>
                    <li class="nav-link scrollto search-bar">
                        <input type="text" placeholder="Search">
                        <button class="search-btn">Search</button>
                    </li>
                    <li><a class="nav-link scrollto " href="subscribe.php">Subscribe</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
        </div>
    </header><!-- End Header -->

    <!-- Main Section -->
    <main id="main">
        <div class="container container-center">
            <div class="row">
                <!-- Box-like container for song information -->
                <div class="col-md-6 song-container">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>" alt="<?php echo $title; ?>" class="img-fluid">
                    <h2><?php echo $title; ?></h2>
                    <p>Artist: <?php echo $artistName; ?></p>
                    <p>Duration: <?php echo $duration; ?></p>
                    <p>Release Date: <?php echo $releaseDate; ?></p>
                    <form method="post">
                        <button type="submit" name="play" class="play-button">Play</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
            <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button>
        </div>
    </footer><!-- End Footer -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <!-- Prevent back button navigation -->
    <script type="text/javascript">
        (function() {
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
        })();
    </script>

</body>

</html>
