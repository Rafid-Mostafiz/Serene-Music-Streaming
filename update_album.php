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

// Check if form is submitted and process the update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $album_id = $_POST['album_id'];
    $album_title = $_POST['album_title'];
    $release_date = $_POST['release_date'];
    
    // Handle image upload if needed
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['tmp_name'];
        $imageData = addslashes(file_get_contents($image));
        
        // Update query with image
        $sql = "UPDATE Album SET AlbumTitle='$album_title', ReleaseDateAlbum='$release_date', ImageData='$imageData' WHERE AlbumID=$album_id";
    } else {
        // Update query without image
        $sql = "UPDATE Album SET AlbumTitle='$album_title', ReleaseDateAlbum='$release_date' WHERE AlbumID=$album_id";
    }
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to Albums page after successful update
        header("Location: Album_List.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch current album details based on album_id
if (isset($_GET['album_id'])) {
    $album_id = $_GET['album_id'];
    $sql = "SELECT * FROM Album WHERE AlbumID = $album_id";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display form for updating album details
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Album - Admin Panel</title>
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
    </style>
</head>
<body>
    <!-- Header -->
    <header id="header" class="fixed-top header-inner-pages">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="Admin_Home.php">Syŕene</a></h1>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="Admin_Home.php">Home</a></li>
                    <li class="dropdown"><a href="#"><span>All</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="Songs.php">Songs</a></li>
                            <li><a href="Artists.php">Artists</a></li>
                            <li><a href="Albums.php">Albums</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span>Add</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="Add_Song.php">Song</a></li>
                            <li><a href="Add_Artist.php">Artist</a></li>
                            <li><a href="Add_Album.php">Album</a></li>
                        </ul>
                    </li>
                    <li><a class="nav-link scrollto  " href="feedback.php">Feedback</a></li>
                    <li><a class="nav-link scrollto  " href="revenue.php">Revenue</a></li>
                    <li class="user-options"><a href="logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
        </div>
    </header><!-- End Header -->

    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Update Album</h2>
            <form method="post" action="update_album.php" enctype="multipart/form-data">
                <input type="hidden" name="album_id" value="<?= $row['AlbumID'] ?>">
                <div class="form-group">
                    <label for="album_title">Album Title</label>
                    <input type="text" class="form-control" id="album_title" name="album_title" value="<?= htmlspecialchars($row['AlbumTitle']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" class="form-control" id="release_date" name="release_date" value="<?= $row['ReleaseDateAlbum'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Album Image</label>
                    <input type="file" class="form-control-file" id="image" name="image">
                </div>
                <button type="submit" class="btn btn-submit" name="submit">Update Album</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Footer -->
    <footer id="footer" style="margin-top: 40px;">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

</body>
</html>

<?php
    } else {
        echo "Album not found";
    }
}

$conn->close();
?>
