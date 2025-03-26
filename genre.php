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
$database = "newserene"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to execute a query and fetch results
function executeQuery($conn, $query)
{
    $result = $conn->query($query);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return $result;
}

// Check if a genre is selected, default to 'Pop' if not set
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : 'Pop';

// Use prepared statements to prevent SQL injection
$songsQuery = $conn->prepare("
    SELECT s.*, a.ArtistName, al.AlbumTitle 
    FROM song s 
    JOIN artist a ON s.ArtistID = a.ArtistID 
    JOIN album al ON s.AlbumID = al.AlbumID 
    WHERE s.GenreID IN (SELECT GenreID FROM genre WHERE GenreName = ?)
");
$songsQuery->bind_param("s", $selectedGenre);

// Debugging: Check for errors during query execution
if (!$songsQuery->execute()) {
    die("Execute failed: " . $songsQuery->error);
}

$songsResult = $songsQuery->get_result();

// Debugging: Check for errors in obtaining the result set
if (!$songsResult) {
    die("Getting result set failed: " . $songsQuery->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Syŕene</title>
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

  
<header id="header" class="fixed-top header-inner-pages">
  <div class="container d-flex align-items-center justify-content-between">
    <h1 class="logo"><a href="Home_Page.php">Syŕene</a></h1>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto " href="Home_Page.php">Home</a></li>
        
        <li><a class="nav-link scrollto" href="Artists.php">Artists</a></li>
        <li><a class="nav-link scrollto" href="Albums.php">Albums</a></li>
        <li class="dropdown">
          <a class="nav-link scrollto active">
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
<!-- <li class="user-options"><a href="logout.php">Logout</a></li> -->
        
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>

<main id="main">
    <div class="containerart">
        <?php
        // Fetch the number of songs for the selected genre
        $numSongsQuery = $conn->prepare("
            SELECT COUNT(*) AS numSongs
            FROM song s
            WHERE s.GenreID IN (SELECT GenreID FROM genre WHERE GenreName = ?)
        ");
        $numSongsQuery->bind_param("s", $selectedGenre);

        if (!$numSongsQuery->execute()) {
            die("Execute failed: " . $numSongsQuery->error);
        }

        $numSongsResult = $numSongsQuery->get_result();

        if (!$numSongsResult) {
            die("Getting result set failed: " . $numSongsQuery->error);
        }

        $numSongsRow = $numSongsResult->fetch_assoc();
        $numSongs = $numSongsRow['numSongs'];
        ?>

        <h1 class="mt-5"><?php echo ucwords($selectedGenre); ?> Songs (<?php echo $numSongs; ?> songs)</h1>

        <!-- Song List -->
        <div class="song-list mt-4">
            <?php while ($row = $songsResult->fetch_assoc()): ?>
              <a href="song.php?song_id=<?php echo $row['SongID']; ?>" class="song">
    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['ImageData']); ?>" alt="Album Art for <?php echo $row['Title']; ?>">
    <div class="song-details">
        <div class="song-name"><?php echo $row['Title']; ?></div>
    </div>
</a>

            <?php endwhile; ?>
        </div>
    </div>
</main>


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
<!-- ======= Footer ======= -->
<footer id="footer">
  <div class="containerf">
    <div class="copyright">
      &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
    </div>
    <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button>
  </div>
</footer>
</body>

</html>