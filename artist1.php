<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;}
$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$artistId = isset($_GET['artistId']) ? intval($_GET['artistId']) : 1;

// Fetch artist details
$artistQuery = "SELECT * FROM artist WHERE ArtistID = ?";
$stmt = $conn->prepare($artistQuery);
$stmt->bind_param("i", $artistId);
$stmt->execute();
$artistResult = $stmt->get_result();

if ($artistResult->num_rows > 0) {
    $artistRow = $artistResult->fetch_assoc();
    $artistName = htmlspecialchars($artistRow['ArtistName']);

    // Load image from Blob if ImageData is present
    $artistImage = $artistRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($artistRow['ImageData']) : 'assets/img/default_artist_image.jpg';
}

// Fetch songs of the selected artist
$songsQuery = "SELECT * FROM song WHERE ArtistID = ?";
$stmtSongs = $conn->prepare($songsQuery);
$stmtSongs->bind_param("i", $artistId);
$stmtSongs->execute();
$songsResult = $stmtSongs->get_result();

$artistSongs = [];

if ($songsResult->num_rows > 0) {
    while ($songRow = $songsResult->fetch_assoc()) {
        $artistSongs[] = [
            'id' => $songRow['SongID'],
            'name' => htmlspecialchars($songRow['Title']),
            'album' => htmlspecialchars($songRow['AlbumID']),
            'image' => $songRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($songRow['ImageData']) : 'assets/img/default_song_image.jpg',
        ];
    }
}

// Fetch recommended artists based on the same language
$recommendedQuery = "SELECT * FROM artist WHERE LanguageID = ? AND ArtistID != ? LIMIT 3";
$stmtRecommended = $conn->prepare($recommendedQuery);
$stmtRecommended->bind_param("ii", $artistRow['LanguageID'], $artistId);
$stmtRecommended->execute();
$recommendedResult = $stmtRecommended->get_result();

$recommendedArtists = [];

if ($recommendedResult->num_rows > 0) {
    while ($recommendedRow = $recommendedResult->fetch_assoc()) {
        $recommendedArtists[] = [
            'id' => $recommendedRow['ArtistID'],
            'name' => htmlspecialchars($recommendedRow['ArtistName']),
            'image' => $recommendedRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($recommendedRow['ImageData']) : 'assets/img/default_artist_image.jpg',
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Syŕene - Artist</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
        /* Additional CSS for fixed artist image size */
        .artist-image {
            max-width: 100%;
            height: auto;
            max-height: 300px; /* Set your desired maximum height */
        }

        /* Additional CSS for displaying "No songs available" message */
        .no-songs-message {
            font-style: italic;
            color: #888;
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
                    <li><a class="nav-link scrollto active" href="Albums.php">Albums</a></li>
                    <li class="dropdown"><a class="nav-link scrollto"><span>Genre</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="genre.php">Pop</a></li>
                            <li><a href="#">Rock</a></li>
                            <li><a href="#">Hip-Hop</a></li>
                           
                        </ul>
                    </li>
                    <li><a class="nav-link scrollto" href="Songs.php">Songs</a></li>
                    <li><a class="nav-link scrollto" href="Display_delete.php">What's new?</a></li>
                    <li class="nav-link scrollto search-bar">
  <form action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search" required>
    <button type="submit" class="search-btn">Search</button>
  </form>
</li>
<li><a class="nav-link scrollto " href="subscribe.php">Subscribe</a></li>

<!-- <li class="user-options"><a href="logout.php">Logout</a></li> -->
                    
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
        </div>
    </header><!-- End Header -->

    <main id="main">
        <div class="containerart">
            <div class="row">
                <!-- Artist's Songs List -->
                <div class="col-lg-6">
                    <h1><?php echo $artistName; ?></h1>
                    <!-- Display Artist Image with fixed size -->
                    <img src="<?php echo $artistImage; ?>" alt="<?php echo $artistName; ?>" class="artist-image">
                    <div class="song-list mt-4">
                        <?php if (count($artistSongs) > 0) : ?>
                            <?php foreach ($artistSongs as $song): ?>
                                <a href="song.php?song_id=<?php echo $song['id']; ?>" class="song">
                                    <!-- Display Song Image (if available) -->
                                    <img src="<?php echo $song['image']; ?>" alt="<?php echo $song['name']; ?>">
                                    <div class="song-details">
                                        <div class="song-name"><?php echo $song['name']; ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="no-songs-message">No songs by this artist available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Other Artist's Recommendations -->
                <div class="col-lg-6">
                    <div class="container">
                        <!-- Recommended Artists Section -->
                        <div class="section" id="artists">
                            <h2>Recommended Artists</h2>
                            <div class="row">
                                <?php foreach ($recommendedArtists as $recommendedArtist): ?>
                                    <div class="col-md-6 mb-4">
                                        <a href="artist1.php?artistId=<?php echo $recommendedArtist['id']; ?>">
                                            <div class="card">
                                                <!-- Display Recommended Artist Image (if available) -->
                                                <img src="<?php echo $recommendedArtist['image']; ?>" class="card-img-top" alt="<?php echo $recommendedArtist['name']; ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $recommendedArtist['name']; ?></h5>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="containerf">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
            <button></button>
        </div>
    </footer><!-- End Footer -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/purecounter/purecounter.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
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
