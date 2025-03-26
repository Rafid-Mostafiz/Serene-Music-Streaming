<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

// Establishing connection
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching data for dropdowns
$languages = $conn->query("SELECT LanguageID, LanguageName FROM songlanguage");
$albums = $conn->query("SELECT AlbumID, AlbumTitle FROM album");
$genres = $conn->query("SELECT GenreID, GenreName FROM genre");
$artists = $conn->query("SELECT ArtistID, ArtistName FROM artist");

// Check if form is submitted and process the update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $song_id = $_POST['song_id'];
    $language_id = $_POST['language_id'];
    $album_id = $_POST['album_id'];
    $genre_id = $_POST['genre_id'];
    $artist_id = $_POST['artist_id'];
    $duration = $_POST['duration'];
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];

    // Handle image upload if needed
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['tmp_name'];
        $imageData = addslashes(file_get_contents($image));
        $imagePath = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($image, $imagePath);

        // Update query with image
        $sql = "UPDATE Song SET LanguageID='$language_id', AlbumID='$album_id', GenreID='$genre_id', ArtistID='$artist_id', 
                Duration='$duration', Title='$title', ReleaseDate='$release_date', ImagePath='$imagePath', ImageData='$imageData' 
                WHERE SongID=$song_id";
    } else {
        // Update query without image
        $sql = "UPDATE Song SET LanguageID='$language_id', AlbumID='$album_id', GenreID='$genre_id', ArtistID='$artist_id', 
                Duration='$duration', Title='$title', ReleaseDate='$release_date' 
                WHERE SongID=$song_id";
    }

    if ($conn->query($sql) === TRUE) {
        // Redirect to Song List page after successful update
        header("Location: Song_List.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch current song details based on song_id
if (isset($_GET['song_id'])) {
    $song_id = $_GET['song_id'];
    $sql = "SELECT * FROM Song WHERE SongID = $song_id";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Song - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        body {
            padding-top: 70px;
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            margin-bottom: 100px; /* Added margin to prevent overlap with footer */
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-submit {
            background-color: #3498db !important;
            color: white !important;
            border-color: #3498db !important;
        }
        .btn-submit:hover {
            background-color: #2980b9 !important;
        }
        footer {
            margin-top: 40px;
            position: fixed;
            width: 100%;
            bottom: 0;
            background: #f9f9f9;
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
                    <li class="dropdown"><a class="nav-link scrollto active"><span>All</span> <i class="bi bi-chevron-down"></i></a>
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

    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Update Song</h2>
            <form method="post" action="update_song.php" enctype="multipart/form-data">
                <input type="hidden" name="song_id" value="<?= $row['SongID'] ?>">
                <div class="form-group">
                    <label for="language_id">Language</label>
                    <select class="form-control" id="language_id" name="language_id" required>
                        <option value="">Select Language</option>
                        <?php while($lang = $languages->fetch_assoc()): ?>
                            <option value="<?= $lang['LanguageID'] ?>" <?= ($lang['LanguageID'] == $row['LanguageID']) ? 'selected' : '' ?>><?= $lang['LanguageName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="album_id">Album</label>
                    <select class="form-control" id="album_id" name="album_id" required>
                        <option value="">Select Album</option>
                        <?php while($alb = $albums->fetch_assoc()): ?>
                            <option value="<?= $alb['AlbumID'] ?>" <?= ($alb['AlbumID'] == $row['AlbumID']) ? 'selected' : '' ?>><?= $alb['AlbumTitle'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="genre_id">Genre</label>
                    <select class="form-control" id="genre_id" name="genre_id" required>
                        <option value="">Select Genre</option>
                        <?php while($gen = $genres->fetch_assoc()): ?>
                            <option value="<?= $gen['GenreID'] ?>" <?= ($gen['GenreID'] == $row['GenreID']) ? 'selected' : '' ?>><?= $gen['GenreName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="artist_id">Artist</label>
                    <select class="form-control" id="artist_id" name="artist_id" required>
                        <option value="">Select Artist</option>
                        <?php while($art = $artists->fetch_assoc()): ?>
                            <option value="<?= $art['ArtistID'] ?>" <?= ($art['ArtistID'] == $row['ArtistID']) ? 'selected' : '' ?>><?= $art['ArtistName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" class="form-control" id="duration" name="duration" value="<?= $row['Duration'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= $row['Title'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" class="form-control" id="release_date" name="release_date" value="<?= $row['ReleaseDate'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" class="form-control-file" id="image" name="image">
                    <small>Current Image: <?= basename($row['ImagePath']) ?></small>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-submit">Update Song</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
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
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
    } else {
        echo "No song found with ID $song_id";
    }
} else {
    echo "No song ID provided";
}
$conn->close();
?>
