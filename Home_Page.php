<?php
session_start();
session_regenerate_id(true); // Prevent session fixation attacks

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function executeQuery($conn, $query) {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return $result;
}

$latestSongsQuery = "SELECT * FROM song ORDER BY ClickCount DESC LIMIT 3";
$latestSongsResult = executeQuery($conn, $latestSongsQuery);

$featuredArtistsQuery = "SELECT artist.*, COUNT(song.ArtistID) AS song_count 
                        FROM artist 
                        LEFT JOIN song ON artist.ArtistID = song.ArtistID 
                        GROUP BY artist.ArtistID 
                        ORDER BY song_count DESC LIMIT 3";
$featuredArtistsResult = executeQuery($conn, $featuredArtistsQuery);

$latestAlbumsQuery = "SELECT album.* FROM album ORDER BY ReleaseDateAlbum DESC LIMIT 3";
$latestAlbumsResult = executeQuery($conn, $latestAlbumsQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Syŕene</title>

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
</head>

<body>

  <!-- ======= Header ======= -->
<header id="header" class="fixed-top header-inner-pages">
  <div class="container d-flex align-items-center justify-content-between">
    <h1 class="logo"><a href="Home_Page.php">Syŕene</a></h1>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto active" href="Home_Page.php">Home</a></li>
        
        <li><a class="nav-link scrollto" href="Artists.php">Artists</a></li>
        <li><a class="nav-link scrollto" href="Albums.php">Albums</a></li>
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
        <li><a class="nav-link scrollto" href="Songs.php">Songs</a></li>
       
     
        <li class="nav-link scrollto search-bar">
  <form action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search" required>
    <button type="submit" class="search-btn">Search</button>
  </form>
</li>
<li><a class="nav-link scrollto " href="subscribe.php">Subscribe</a></li>
        <li class="user-options"><a href="logout.php">Logout</a></li>
        
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>


  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex flex-column align-items-center justify-content-center">
    <img src="assets/img/home.jpg" alt="Hero Image">
    <h2>Welcome to Syŕene</h2>
    <p>WHEN WORDS FAIL, MUSIC SPEAKS</p>
  </section><!-- End Hero -->

  <main id="main">

    <div class="container">
      <!-- Latest Songs Section -->
      <div class="section" id="trending">
          <h2>Trending songs</h2>
          <div class="row">
              <?php while ($row = $latestSongsResult->fetch_assoc()): ?>
                  <div class="col-md-4">
                      <a href="song.php?song_id=<?php echo $row['SongID']; ?>">
                          <div class="card">
                              <img src="data:image/jpeg;base64,<?php echo base64_encode($row['ImageData']); ?>" class="card-img-top" alt="<?php echo $row['Title']; ?>">
                              <div class="card-body">
                                  <h5 class="card-title"><?php echo $row['Title']; ?></h5>
                              </div>
                          </div>
                      </a>
                  </div>
              <?php endwhile; ?>
          </div>
      </div>

      <!-- Featured Artists Section -->
      <div class="section" id="artists">
          <h2>Featured Artists</h2>
          <div class="row">
              <?php while ($row = $featuredArtistsResult->fetch_assoc()): ?>
                  <div class="col-md-4">
                      <a href="artist1.php?id=<?php echo $row['ArtistID']; ?>">
                          <div class="card">
                              <img src="data:image/jpeg;base64,<?php echo base64_encode($row['ImageData']); ?>" class="card-img" alt="<?php echo $row['ArtistName']; ?>">
                              <div class="card-body">
                                  <h5 class="card-title"><?php echo $row['ArtistName']; ?></h5>
                              </div>
                          </div>
                      </a>
                  </div>
              <?php endwhile; ?>
          </div>
      </div>

      <!-- Latest Albums Section -->
      <div class="section" id="albums">
          <h2>Latest Albums</h2>
          <div class="row">
              <?php while ($row = $latestAlbumsResult->fetch_assoc()): ?>
                  <div class="col-md-4">
                      <a href="album1.php?id=<?php echo $row['AlbumID']; ?>">
                          <div class="card">
                              <img src="data:image/jpeg;base64,<?php echo base64_encode($row['ImageData']); ?>" class="card-img-top" alt="<?php echo $row['AlbumTitle']; ?>">
                              <div class="card-body">
                                  <h5 class="card-title"><?php echo $row['AlbumTitle']; ?></h5>
                              </div>
                          </div>
                      </a>
                  </div>
              <?php endwhile; ?>
          </div>
      </div>
    </div>
  </main>

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
