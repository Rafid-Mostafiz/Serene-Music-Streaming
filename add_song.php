<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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
</head>

<body>

  <!-- ======= Header ======= -->
<header id="header" class="fixed-top header-inner-pages">
  <div class="container d-flex align-items-center justify-content-between">
    <h1 class="logo"><a href="index.php">Syŕene</a></h1>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
        
        <li><a class="nav-link scrollto" href="artist.php">Artists</a></li>
        <li><a class="nav-link scrollto" href="Album.html">Albums</a></li>
        <li class="dropdown">
          <a class="nav-link scrollto">
            <span>Genre</span> <i class="bi bi-chevron-down"></i>
          </a>
          <ul>
            <?php
            // Use prepared statement to fetch genres with at least 1 song
            $genresQuery = "SELECT g.GenreName FROM genre g
                            JOIN song s ON g.GenreID = s.GenreID
                            GROUP BY g.GenreID";
            $genresResult = executeQuery($conn, $genresQuery);

            while ($genreRow = $genresResult->fetch_assoc()) :
            ?>
              <li><a href="genre.php?genre=<?php echo $genreRow['GenreName']; ?>"><?php echo $genreRow['GenreName']; ?></a></li>
            <?php endwhile; ?>
          </ul>
        </li>
        <li><a class="nav-link scrollto" href="Add_song.php">Songs</a></li>
        <li><a class="nav-link scrollto" href="Display_delete.php">Library</a></li>
     
        <li class="nav-link scrollto search-bar">
          <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search">
            <button type="submit" class="search-btn">Search</button>
          </form>
        </li>
        <li class="user-options"><a href="login.php">Log in</a><span class="separator">||</span></li>
        <li class="user-options"><a href="registration.php">Register</a></li>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>






<footer id="footer">
  <div class="containerf">
    <div class="copyright">
      &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
    </div>
    <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button>
  </div>
</footer>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>