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

$albumId = isset($_GET['albumId']) ? intval($_GET['albumId']) : 1;

// Fetch album details
$albumQuery = "SELECT * FROM album WHERE AlbumID = ?";
$stmt = $conn->prepare($albumQuery);
$stmt->bind_param("i", $albumId);
$stmt->execute();
$albumResult = $stmt->get_result();

if ($albumResult->num_rows > 0) {
    $albumRow = $albumResult->fetch_assoc();
    $albumTitle = htmlspecialchars($albumRow['AlbumTitle']);

    // Load image from Blob if ImageData is present
    $albumImage = $albumRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($albumRow['ImageData']) : 'assets/img/default_album_image.jpg';
}

// Fetch songs of the selected album
$songsQuery = "SELECT * FROM song WHERE AlbumID = ?";
$stmtSongs = $conn->prepare($songsQuery);
$stmtSongs->bind_param("i", $albumId);
$stmtSongs->execute();
$songsResult = $stmtSongs->get_result();

$albumSongs = [];

if ($songsResult->num_rows > 0) {
    while ($songRow = $songsResult->fetch_assoc()) {
        $albumSongs[] = [
            'id' => $songRow['SongID'],
            'name' => htmlspecialchars($songRow['Title']),
            'artist' => htmlspecialchars($songRow['ArtistID']),
            'image' => $songRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($songRow['ImageData']) : 'assets/img/default_song_image.jpg',
        ];
    }
}

// Fetch recommended albums (assuming it's based on the same language)
$recommendedQuery = "SELECT * FROM album WHERE LanguageID = ? AND AlbumID != ? LIMIT 3";
$stmtRecommended = $conn->prepare($recommendedQuery);
$stmtRecommended->bind_param("ii", $albumRow['LanguageID'], $albumId);
$stmtRecommended->execute();
$recommendedResult = $stmtRecommended->get_result();

$recommendedAlbums = [];

if ($recommendedResult->num_rows > 0) {
    while ($recommendedRow = $recommendedResult->fetch_assoc()) {
        $recommendedAlbums[] = [
            'id' => $recommendedRow['AlbumID'],
            'title' => htmlspecialchars($recommendedRow['AlbumTitle']),
            'image' => $recommendedRow['ImageData'] ? 'data:image/jpeg;base64,' . base64_encode($recommendedRow['ImageData']) : 'assets/img/default_album_image.jpg',
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
    <title>Syŕene - <?php echo $albumTitle; ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
        /* Additional CSS for fixed album image size */
        .album-image {
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
                    
                    <li class="nav-link scrollto search-bar">
  <form action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search" required>
    <button type="submit" class="search-btn">Search</button>
  </form>
</li>
<li><a class="nav-link scrollto " href="subscribe.php">Subscribe</a></li>
<!-- 
<li class="user-options"><a href="logout.php">Logout</a></li> -->
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
        </div>
    </header><!-- End Header -->

    <main id="main">
        <div class="containerart">
            <div class="row">
               <!-- Album's Songs List -->
<div class="col-lg-6">
    <h1><?php echo $albumTitle; ?></h1>
    <!-- Display Album Image with fixed size -->
    <img src="<?php echo $albumImage; ?>" alt="<?php echo $albumTitle; ?>" class="album-image">
    <div class="song-list mt-4">
        <?php if (count($albumSongs) > 0) : ?>
            <?php foreach ($albumSongs as $song): ?>
                <a href="song.php?song_id=<?php echo $song['id']; ?>" class="song">
                    <!-- Display Song Image (if available) -->
                    <img src="<?php echo $song['image']; ?>" alt="<?php echo $song['name']; ?>">
                    <div class="song-details">
                        <div class="song-name"><?php echo $song['name']; ?></div>
                        
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="no-songs-message">No songs of the album available.</p>
        <?php endif; ?>
    </div>
</div>


                <!<!-- Other Album's Recommendations -->
<div class="col-lg-6">
    <div class="container">
        <!-- Recommended Albums Section -->
        <div class="section" id="albums">
            <h2>Recommended Albums</h2>
            <div class="row">
                <?php foreach ($recommendedAlbums as $recommendedAlbum): ?>
                    <div class="col-lg-6 mb-4">
                        <a href="album1.php?albumId=<?php echo $recommendedAlbum['id']; ?>">
                            <div class="card">
                                <!-- Display Recommended Album Image (if available) -->
                                <img src="<?php echo $recommendedAlbum['image']; ?>" class="card-img-top" alt="<?php echo $recommendedAlbum['title']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $recommendedAlbum['title']; ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
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
            <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button>
        </div>
    </footer><!-- End Footer -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

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
