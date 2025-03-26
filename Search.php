
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Syŕene - All Songs</title>
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
            height : 700px;
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
                    <li><a class="nav-link scrollto " href="Songs.php">Songs</a></li>
                    
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

    <!-- ======= Main Section ======= -->
    <section id="main">
        <div class="container">
            <div class="centered-box">
                <div class="box">
                    <h2>Not Found</h2>
                    <?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($input, $conn) {
    return $conn->real_escape_string($input);
}

function findClosestMatchingTitle($query, $conn, $table, $columnName, $idColumn, $page) {
    $query = sanitizeInput($query, $conn);

    $levenshteinQuery = "SELECT $columnName, $idColumn FROM $table";
    $levenshteinResult = $conn->query($levenshteinQuery);

    $suggestedTerm = null;
    $suggestedId = null;
    $minLevenshteinDistance = PHP_INT_MAX;

    while ($row = $levenshteinResult->fetch_assoc()) {
        $title = $row[$columnName];
        $id = $row[$idColumn];

        $levenshteinDistance = levenshtein($query, strtolower($title));

        if (stripos(str_replace(' ', '', $title), str_replace(' ', '', $query)) === 0 && $levenshteinDistance < $minLevenshteinDistance) {
            $suggestedTerm = $title;
            $suggestedId = $id;
            $minLevenshteinDistance = $levenshteinDistance;
        }
    }

    if ($suggestedTerm) {
        return "<a href=\"$page=$suggestedId\">$suggestedTerm</a>";
    }

    return null;
}

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $searchQuery = $_GET['query'];
    $searchQueryEscaped = sanitizeInput($searchQuery, $conn);

    $songQuery = "SELECT * FROM song WHERE LOWER(TRIM(Title)) = LOWER('$searchQueryEscaped')";
    $songResult = $conn->query($songQuery);

    $artistQuery = "SELECT * FROM artist WHERE LOWER(TRIM(ArtistName)) = LOWER('$searchQueryEscaped')";
    $artistResult = $conn->query($artistQuery);

    $albumQuery = "SELECT * FROM album WHERE LOWER(TRIM(AlbumTitle)) = LOWER('$searchQueryEscaped')";
    $albumResult = $conn->query($albumQuery);

    if ($songResult && $songResult->num_rows > 0) {
        $songRow = $songResult->fetch_assoc();
        header("Location: song.php?song_id=" . $songRow['SongID']);
        exit();
    } elseif ($artistResult && $artistResult->num_rows > 0) {
        $artistRow = $artistResult->fetch_assoc();
        header("Location: artist.php?id=" . $artistRow['ArtistID']);
        exit();
    } elseif ($albumResult && $albumResult->num_rows > 0) {
        $albumRow = $albumResult->fetch_assoc();
        header("Location: album1.php?albumId=" . $albumRow['AlbumID']);
        exit();
    } else {
        $suggestedSong = findClosestMatchingTitle($searchQuery, $conn, 'song', 'Title', 'SongID', 'song.php?song_id');
        $suggestedArtist = findClosestMatchingTitle($searchQuery, $conn, 'artist', 'ArtistName', 'ArtistID', 'artist.php?id');
        $suggestedAlbum = findClosestMatchingTitle($searchQuery, $conn, 'album', 'AlbumTitle', 'AlbumID', 'album1.php?albumId');

        $suggestions = [];

        if (!empty($suggestedSong)) {
            $suggestions[] = "song: $suggestedSong";
        }
        if (!empty($suggestedArtist)) {
            $suggestions[] = "artist: $suggestedArtist";
        }
        if (!empty($suggestedAlbum)) {
            $suggestions[] = "album: $suggestedAlbum";
        }
        if (!empty($suggestions)) {
            echo "No exact match found for \"$searchQuery\". Did you mean: " . implode(', ', $suggestions) . "?";
        } else {
            echo "The search term \"$searchQuery\" doesn't exist in songs, artists, or albums.";
        }
       
    }
} 
$conn->close();
?>
                </div>
            </div>
        </div>
    </section><!-- End Main Section -->


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
    <script src="assets/js/main.js"></script></body>

</html>