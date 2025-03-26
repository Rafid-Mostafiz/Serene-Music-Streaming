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

// Function to fetch recently added items from the 'song' table
function fetchRecentlyAddedSongs($conn) {
    $query = "SELECT * FROM song ORDER BY SongID DESC LIMIT 3";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Function to fetch recently added items from the 'artist' table
function fetchRecentlyAddedArtists($conn) {
    $query = "SELECT * FROM artist ORDER BY ArtistID DESC LIMIT 3";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Function to fetch recently added items from the 'album' table
function fetchRecentlyAddedAlbums($conn) {
    $query = "SELECT * FROM album ORDER BY AlbumID DESC LIMIT 3";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Example usage:
$recently_added_songs = fetchRecentlyAddedSongs($conn);
$recently_added_artists = fetchRecentlyAddedArtists($conn);
$recently_added_albums = fetchRecentlyAddedAlbums($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - Syŕene</title>

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
        /* Additional custom styles */
        .recent-items {
            margin-top: 50px;
        }

        .recent-items h2 {
            margin-bottom: 30px;
        }

        .card {
            margin-bottom: 20px;
            height: 100%; /* Ensure each card occupies the same height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center; /* Center align content */
        }

        .card-body {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .song-image {
            width: 100%;
            height: 300px; /* Set a fixed height for all images */
            object-fit: cover; /* Ensure images fill the container */
        }

        .card-title {
            font-weight: bold; /* Make the title text bold */
            margin-top: 10px; /* Adjust margin as needed */
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
                <li><a class="nav-link scrollto active" href="Admin_Home.php">Home</a></li>
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
                <li><a class="nav-link scrollto  " href="feedback.php">Feedback</a></li>
                <li><a class="nav-link scrollto  " href="revenue.php">Revenue</a></li>
                <li><a class="nav-link scrollto " href="logout.php">Logout</a></li> 
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->
    </div>
</header><!-- End Header -->

    <!-- ======= Main Section ======= -->
    <main id="main">

        <!-- ======= Recently Added Songs Section ======= -->
        <section id="recently-added-songs" class="section recent-items">
            <div class="container">
                <h2>Recently Added Songs</h2>
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($recently_added_songs)): ?>
                        <div class="col-md-4">
                            <div class="card">
                                
                                    <img src='data:image/jpeg;base64,<?= base64_encode($row['ImageData']) ?>' class='song-image' alt='Song Image'>
                                </a>
                                <div class="card-body">
                                    <a href="admin_list_of_artists.php?artist_id=<?php echo $row['ArtistID']; ?>"><p><center><b><?php echo htmlspecialchars($row['Title']); ?></b></center></p></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section><!-- End Recently Added Songs Section -->

        <!-- ======= Recently Added Artists Section ======= -->
        <section id="recently-added-artists" class="section recent-items">
            <div class="container">
                <h2>Recently Added Artists</h2>
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($recently_added_artists)): ?>
                        <div class="col-md-4">
                            <div class="card">
                                
                                    <img src='data:image/jpeg;base64,<?= base64_encode($row['ImageData']) ?>' class='song-image' alt='Artist Image'>
                                </a>
                                <div class="card-body">
                                    <a href="admin_list_of_artists.php?artist_id=<?php echo $row['ArtistID']; ?>"><p><center><b><?php echo htmlspecialchars($row['ArtistName']); ?></b></center></p></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section><!-- End Recently Added Artists Section -->

        <!-- ======= Recently Added Albums Section ======= -->
        <section id="recently-added-albums" class="section recent-items">
            <div class="container">
                <h2>Recently Added Albums</h2>
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($recently_added_albums)): ?>
                        <div class="col-md-4">
                            <div class="card">
                                
                                    <img src='data:image/jpeg;base64,<?= base64_encode($row['ImageData']) ?>' class='song-image' alt='Album Image'>
                                </a>
                                <div class="card-body">
                                    <a href="admin_list_of_artists.php?artist_id=<?php echo $row['ArtistID']; ?>"><p><center><b><?php echo htmlspecialchars($row['AlbumTitle']); ?></b></center></p></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section><!-- End Recently Added Albums Section -->

    </main><!-- End #main -->

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
