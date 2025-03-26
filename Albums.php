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

function fetchAlbumData($conn) {
    $data = array();

    $sql = "SELECT * FROM album";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    return $data;
}

$data = fetchAlbumData($conn);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Syŕene - Artist 1</title>
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
        <div class="container mt-5">
            <div class="row">
                <?php foreach ($data as $item) : ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <?php if (isset($item['ImageData'])) : ?>
                                <?php
                                $imageData = base64_encode($item['ImageData']);
                                $imageSrc = "data:image/jpeg;base64," . $imageData;
                                ?>
                                <a href="album1.php?albumId=<?php echo $item['AlbumID']; ?>">
                                    <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="<?php echo isset($item['AlbumTitle']) ? $item['AlbumTitle'] : ''; ?>">
                                </a>
                            <?php endif; ?>
                            <div class="card-body">
                                <?php if (isset($item['AlbumTitle'])) : ?>
                                    <h5 class="card-title">
                                        <a href="album1.php?albumId=<?php echo $item['AlbumID']; ?>"><?php echo $item['AlbumTitle']; ?></a>
                                    </h5>
                                <?php endif; ?>
                                <!-- Add more details as needed -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
