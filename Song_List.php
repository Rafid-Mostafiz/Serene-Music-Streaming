<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$database = "newserene";

// Establishing connection
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $whereClause = "WHERE s.Title LIKE '%$search%' OR a.ArtistName LIKE '%$search%' OR al.AlbumTitle LIKE '%$search%' OR g.GenreName LIKE '%$search%'";
}

// Constructing SQL query
$sql = "SELECT s.*, a.ArtistName, al.AlbumTitle, g.GenreName FROM Song s
        JOIN Artist a ON s.ArtistID = a.ArtistID
        JOIN Album al ON s.AlbumID = al.AlbumID
        JOIN Genre g ON s.GenreID = g.GenreID
        $whereClause
        ORDER BY s.SongID DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Songs - Admin Panel</title>
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

        .song-image {
    width: 100%;
    height: 300px; /* Adjust height as needed */
    object-fit: cover;
}


        .song-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .song-block {
            flex: 0 0 calc(30% - 20px);
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9 !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .song-details {
            padding: 10px;
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-delete {
            background-color: #e74c3c !important;
            color: white !important;
            border-color: #e74c3c !important;
        }

        .btn-update {
            background-color: #3498db !important;
            color: white !important;
            border-color: #3498db !important;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-input {
    width: calc(100% - 130px); /* Adjusted width to match the button */
    padding: 30px;
    border: 1px solid #ccc;
    border-radius: 5px 0 0 5px;
    font-size: 16px;
}

.btn-search {
    width: 130px; /* Adjusted width to match the input */
    padding: 10px 20px;
    border: 1px solid #ccc;
    border-radius: 0 5px 5px 0;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
}


        .btn-search:hover {
            background-color: #0056b3;
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
                <li class="dropdown"><a class="nav-link scrollto active "><span>All</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="Song_List.php">Songs</a></li>
                        <li><a href="Artist_List.php">Artists</a></li>
                        <li><a href="Album_List.php">Albums</a></li>
                    </ul>
                </li>
                <li class="dropdown"><a class="nav-link scrollto "><span>Add</span> <i class="bi bi-chevron-down"></i></a>
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
        <h2 class="mt-4 mb-4 text-center">All Songs</h2>

        <!-- Search form -->
        <form method="get" action="" class="search-form">
    <div class="input-group mb-3">
        <input type="text" class="form-control search-input" placeholder="Search..." name="search"
            value="<?= htmlspecialchars($search) ?>">
        <div class="input-group-append">
            <button class="btn btn-search" type="submit">Search</button>
        </div>
    </div>
</form>


        <div class="song-container">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="song-block">
    <a href='song_page.php?song_id=<?= $row['SongID'] ?>'>
        <img src='data:image/jpeg;base64,<?= base64_encode($row['ImageData']) ?>' class='song-image' alt='Song Image'>
    </a>
                <div class='song-details'>
                    <strong>Title:</strong> <?= $row['Title'] ?><br>
                    <strong>Artist:</strong> <?= $row['ArtistName'] ?><br>
                    <strong>Album:</strong> <?= $row['AlbumTitle'] ?><br>
                    <strong>Release Date:</strong> <?= date('M d, Y', strtotime($row['ReleaseDate'])) ?><br>
                    <strong>Genre:</strong> <?= $row['GenreName'] ?><br>
                    <strong>Duration:</strong> <?= $row['Duration'] ?> seconds<br>

                    <form method='post' action='delete_song.php' style='display:inline;'>
                        <input type='hidden' name='song_id' value='<?= $row['SongID'] ?>'>
                        <button type='submit' class='btn btn-delete'
                            onclick='return confirm("Are you sure you want to delete this song?")'>Delete</button>
                    </form>

                    <form method='get' action='update_song.php' style='display:inline;'>
                        <input type='hidden' name='song_id' value='<?= $row['SongID'] ?>'>
                        <button type='submit' class='btn btn-update'>Update</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>

    <!-- ======= Footer ======= -->
    <footer id="footer" style="margin-top: 40px;">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Vendor JS Files -->
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/purecounter/purecounter.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>

<?php
$result->free();
$conn->close();
?>
