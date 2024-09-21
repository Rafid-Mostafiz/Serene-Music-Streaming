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

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function executeQuery($conn, $query)
{
    $result = $conn->query($query);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return $result;
}

// Initialize an empty variable to store the thank you message
$thankYouMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form submitted, process the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // Use prepared statement to prevent SQL injection
    $insertQuery = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertQuery);

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Set session variable for success message
        $_SESSION['thankYouMessage'] = "Your message has been sent. Thank you!";
        
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Insertion failed: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Check if there is a thank you message in session and assign it
if (isset($_SESSION['thankYouMessage'])) {
    $thankYouMessage = $_SESSION['thankYouMessage'];
    unset($_SESSION['thankYouMessage']); // Clear the session variable
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
    <style>
        .contact-form {
            margin-top: 30px; /* Adjust as needed */
        }

        .contact-form textarea {
            resize: vertical; /* Allow vertical resizing */
        }

        .contact-form button {
            margin-top: 10px; /* Spacing below textarea */
            padding: 10px 20px; /* Adjust padding as needed */
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            border: none; /* No border */
            cursor: pointer; /* Pointer cursor */
            border-radius: 5px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .contact-form button:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        .alert {
            margin-top: 10px; /* Spacing above confirmation message */
            padding: 10px;
            background-color: #f2f2f2; /* Light gray background */
            border: 1px solid #ddd; /* Light gray border */
            border-radius: 5px; /* Rounded corners */
        }

        .alert-success {
            background-color: #dff0d8; /* Greenish background for success */
            border-color: #d6e9c6; /* Greenish border for success */
            color: #3c763d; /* Dark green text for success */
        }
    </style>
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
        
        <!-- <li class="user-options"><a href="logout.php">Logout</a></li> -->
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>

<main id="main">
  
  <section id="contact" class="contact section-bg">
    <div class="container-fluid">

      <div class="section-title">
        <h2>Contact</h2>
        <h3>Get In Touch With <span>Us</span></h3>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-10">
          <div class="row">

            <div class="col-lg-6">

              <div class="row justify-content-center">

                <div class="col-md-6 info d-flex flex-column align-items-stretch">
                  <i class="bx bx-map"></i>
                  <h4>Address</h4>
                  <p>Ahsanullah University of Science and Technology<br>141 & 142, Love Road, Tejgaon Industrial Area, Dhaka-1208</p>
                </div>
                <div class="col-md-6 info d-flex flex-column align-items-stretch">
                  <i class="bx bx-phone"></i>
                  <h4>Call Us</h4>
                  <p>+8801*********<br>+8801*********</p>
                </div>
                <div class="col-md-6 info d-flex flex-column align-items-stretch">
                  <i class="bx bx-envelope"></i>
                  <h4>Email Us</h4>
                  <p>info@aust.edu<br> regr@aust.edu</p>
                </div>
                <div class="col-md-6 info d-flex flex-column align-items-stretch">
                  <i class="bx bx-time-five"></i>
                  <h4>Working Hours</h4>
                  <p>Sun - Thu: 9AM to 5PM<br>Saturday: 9AM to 1PM</p>
                </div>

              </div>

            </div>
             
            <div class="col-lg-6">
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" role="form" class="contact-form">
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" class="form-control" id="name" required>
                  </div>
                  <div class="col-md-6 form-group mt-3 mt-md-0">
                    <label for="email">Your Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                  </div>
                </div>
                <div class="form-group mt-3">
                  <label for="subject">Subject</label>
                  <input type="text" class="form-control" name="subject" id="subject" required>
                </div>
                <div class="form-group mt-3">
                  <label for="message">Message</label>
                  <textarea class="form-control" name="message" rows="8" required></textarea>
                </div>
                <div class="text-center">
                  <button type="submit">Send Message</button>
                </div>
                
                <!-- Display confirmation message -->
                <?php if (!empty($thankYouMessage)) : ?>
                  <div class="alert alert-success mt-3"><?php echo $thankYouMessage; ?></div>
                <?php endif; ?>
              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>

</main><!-- End #main -->

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
 