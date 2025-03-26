<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "newserene";

$conn = new mysqli($servername, $username, $password, $database);

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

function validatePassword($password)
{
    // Password must be at least 8 characters long
    return strlen($password) >= 8;
}

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = strtolower($_POST["email"]); // Convert email to lowercase
    $password = $_POST["password"];

    // Check if the email already exists (case insensitive)
    $checkEmailQuery = "SELECT * FROM sereneuser WHERE LOWER(Email) = LOWER('$email')";
    $existingUser = executeQuery($conn, $checkEmailQuery)->fetch_assoc();

    if ($existingUser) {
        // Email already exists, show error message
        $errorMsg = "Error: This email is already registered.";
        $_SESSION['errorMsg'] = $errorMsg;
    } elseif (!validatePassword($password)) {
        // Password validation failed
        $errorMsg = "Error: Password must be at least 8 characters long.";
        $_SESSION['errorMsg'] = $errorMsg;
    } else {
        // Insert user data into the sereneuser table without hashing the password (for demonstration purposes)
        $insertUserQuery = "INSERT INTO sereneuser (Email, UserName, Password) VALUES ('$email', '$firstName $lastName', '$password')";
        executeQuery($conn, $insertUserQuery);

        // Redirect the user to the login page after successful registration
        header("Location: login.php");
        exit();
    }

    // Redirect back to registration page with error message
    header("Location: registration.php");
    exit();
}

// Check for stored error message in session
if (isset($_SESSION['errorMsg'])) {
    $errorMsg = $_SESSION['errorMsg'];
    unset($_SESSION['errorMsg']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syŕene - Registration</title>

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
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa; /* Set your desired background color */
            color: #495057;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        #registration-container {
            background-color: #ffffff; /* Set your desired container background color */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px; /* Adjust the max-width as needed */
            width: 100%;
        }

        #registration h2 {
            margin-bottom: 20px;
            color: #343a40; /* Set your desired heading color */
        }

        #registration label {
            display: block;
            margin-bottom: 10px;
        }

        #registration input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #registration button {
            background-color: #007bff; /* Set your desired button color */
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #registration button:hover {
            background-color: #0056b3; /* Set your desired button hover color */
        }

        .error-box {
            background-color: #C70039;
            color: #FAFAFA ;
            padding: 10px;
            border: 1px solid #FF5733 ;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<header id="header" class="fixed-top header-inner-pages">
  <div class="container d-flex align-items-center justify-content-between">
    <h1 class="logo"><a>Syŕene</a></h1>

    <nav id="navbar" class="navbar">
      <ul>
        <!-- <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
        
        <li><a class="nav-link scrollto" href="allartist.php">Artists</a></li>
        <li><a class="nav-link scrollto" href="song_album_artst.php">Albums</a></li>
        <li class="dropdown">
          <a class="nav-link scrollto">
            <span>Genre</span> <i class="bi bi-chevron-down"></i>
          </a>
          <ul>
           
          </ul>
        </li>
        <li><a class="nav-link scrollto" href="allsong.php">Songs</a></li>
        <li><a class="nav-link scrollto" href="Display_delete.php">Library</a></li>
     
        <li class="nav-link scrollto search-bar">
          <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search">
            <button type="submit" class="search-btn">Search</button>
          </form>
        </li> -->
        <li class="user-options"><a href="login.php">Log in</a></li>
        <li class="user-options"><a href="registration.php">Register</a></li>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>

    <!-- ======= Registration Section ======= -->

<section id="registration-container" class="d-flex flex-column align-items-center justify-content-center">
    <h2>Create an Account</h2>

    <?php if (!empty($errorMsg)): ?>
        <div class="error-box"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <form id="registration" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" required>

        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <div class="text-center">
            <button type="submit">Register</button>
        </div>
    </form>
    <p style="margin-top: 15px;">Already registered? <a href="login.php">Login</a></p>
</section><!-- End Registration -->

<footer id="footer">
  <div class="containerf">
    <div class="copyright">
      &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
    </div>
    <!-- <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button> -->
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
