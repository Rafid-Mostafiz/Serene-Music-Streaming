<?php
session_start();

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

// Fetch user details
$userID = $_SESSION['UserID'];
$userQuery = "SELECT UserName, Email FROM sereneuser WHERE UserID = '$userID'";
$userResult = executeQuery($conn, $userQuery);
$userData = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Syŕene - Subscribe</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
    <style>
        /* Custom CSS for Subscription Page */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .subscription-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .subscription-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
            color: #333;
        }

        .subscription-plan {
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #f9f9f9;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .subscription-plan:hover {
            transform: scale(1.02);
        }

        .subscription-plan h3 {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #555;
        }

        .subscription-plan p {
            font-size: 1.2em;
            color: #777;
            margin-bottom: 30px;
        }

        .subscription-plan .plan-price {
            font-weight: bold;
            font-size: 1.5em;
            color: #333;
        }

        .proceed-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .proceed-btn:hover {
            background-color: #333;
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
                <li><a class="nav-link scrollto active" href="subscribe.php">Subscribe</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
    </div>
</header>

<main id="main">
    <div class="subscription-container">
        <h2>Subscription Plans</h2>

        <div class="subscription-plan">
            <h3>Monthly Plan</h3>
            <p>Get unlimited access for just $12 per month. Enjoy ad-free streaming and high-quality downloads.</p>
        </div>

        <div class="subscription-plan">
            <h3>Yearly Plan</h3>
            <p>Save with our yearly plan! Pay only $100 per year for unlimited access to our entire music library.</p>
        </div>

        <button class="proceed-btn" onclick="proceedToPayment()">Proceed</button>
    </div>
</main>

<footer id="footer">
    <div class="container">
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

    function proceedToPayment() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payment.php';

        // Default plan value
        const inputPlan = document.createElement('input');
        inputPlan.type = 'hidden';
        inputPlan.name = 'plan';
        inputPlan.value = 'monthly'; // Default plan
        form.appendChild(inputPlan);

        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = 'name';
        inputName.value = "<?php echo $userData['UserName']; ?>";
        form.appendChild(inputName);

        const inputEmail = document.createElement('input');
        inputEmail.type = 'hidden';
        inputEmail.name = 'email';
        inputEmail.value = "<?php echo $userData['Email']; ?>";
        form.appendChild(inputEmail);

        document.body.appendChild(form);
        form.submit();
    }
</script>
</body>
</html>

