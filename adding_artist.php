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

// Check if form is submitted and process the insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $artist_name = $_POST['artist_name'];
    $language_id = $_POST['language_id'];
    $country = $_POST['country'];
    $image_path = ""; // default if no image uploaded
    $image_data = ""; // default if no image uploaded

    // Handle image upload if needed
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['tmp_name'];
        $image_data = addslashes(file_get_contents($image));
        $image_path = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($image, $image_path);
    }

    // Insert query
    $sql = "INSERT INTO Artist (ArtistName, LanguageID, Country, ImagePath, ImageData) 
            VALUES ('$artist_name', '$language_id', '$country', '$image_path', '$image_data')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to Artists page after successful insertion
        header("Location: Artist_List.php");
        exit();
    } else {
        echo "Error adding record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artist - Admin Panel</title>
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
            margin-bottom: 50px; /* Add bottom margin to create space between the form and footer */
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-submit {
            background-color: #4caf50 !important;
            color: white !important;
            border-color: #4caf50 !important;
        }

        .btn-submit:hover {
            background-color: #45a049 !important;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
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
                <li class="dropdown"><a class="nav-link scrollto "><span>All</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="Song_List.php">Songs</a></li>
                        <li><a href="Artist_List.php">Artists</a></li>
                        <li><a href="Album_List.php">Albums</a></li>
                    </ul>
                </li>
                <li class="dropdown"><a class="nav-link scrollto active"><span>Add</span> <i class="bi bi-chevron-down"></i></a>
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
            <h2 class="form-header">Add Artist</h2>
            <form method="post" action="adding_artist.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="artist_name">Artist Name</label>
                    <input type="text" class="form-control" id="artist_name" name="artist_name" required>
                </div>
                <div class="form-group">
                    <label for="language_id">Language</label>
                    <select class="form-control" id="language_id" name="language_id" required>
                        <option value="">Select Language</option>
                        <?php
                        // Reset the pointer for languages dropdown
                        $languages->data_seek(0);
                        while ($row = $languages->fetch_assoc()) :
                        ?>
                            <option value="<?= $row['LanguageID'] ?>"><?= $row['LanguageName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="image">Artist Image</label>
                    <input type="file" class="form-control-file" id="image" name="image">
                </div>
                <button type="submit" class="btn btn-submit" name="submit">Add Artist</button>
            </form>
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
