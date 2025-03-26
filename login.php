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

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Use prepared statement to avoid SQL injection
    $checkUserQuery = "SELECT * FROM sereneuser WHERE Email = ? LIMIT 1";
    $stmt = $conn->prepare($checkUserQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();

    if ($userResult) {
        // Verify password (assuming passwords are stored in plaintext for this example; use hashed passwords in production)
        if ($password === $userResult['Password']) {
            $_SESSION['UserID'] = $userResult['UserID'];
            $_SESSION['username'] = $userResult['UserName'];
            $_SESSION['loggedin'] = true; 

            // Update LoggedIn status in database
            $userID = $userResult['UserID'];
            $updateLoginQuery = "UPDATE sereneuser SET LoggedIn = 1 WHERE UserID = ?";
            $stmt = $conn->prepare($updateLoginQuery);
            $stmt->bind_param("i", $userID);
            $stmt->execute();

            // Check isAdmin status
            $isAdmin = $userResult['isAdmin'];
            if ($isAdmin == 1) {
                header("Location: Admin_Home.php");
                exit();
            } else {
                header("Location: Home_Page.php");
                exit();
            }
        } else {
            $errorMsg = "Error: Invalid email or password.";
        }
    } else {
        $errorMsg = "Error: Invalid email or password.";
    }
    
    $_SESSION['errorMsg'] = $errorMsg;
    
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    $userID = $_SESSION['UserID'];
    $updateLogoutQuery = "UPDATE sereneuser SET LoggedIn = 0 WHERE UserID = ?";
    $stmt = $conn->prepare($updateLogoutQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    
    // Clear session data
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Syŕene - Login</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        #login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        #login h2 {
            margin-bottom: 20px;
            color: #343a40;
        }

        #login label {
            display: block;
            margin-bottom: 10px;
        }

        #login input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #login button {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #login button:hover {
            background-color: #0056b3;
        }

        .error-box {
            background-color: #C70039;
            color: #FAFAFA;
            padding: 10px;
            border: 1px solid #FF5733;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <header id="header" class="fixed-top header-inner-pages">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="#">Syŕene</a></h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li class="user-options"><a href="login.php">Log in</a></li>
                    <li class="user-options"><a href="registration.php">Register</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <section id="login-container" class="d-flex flex-column align-items-center justify-content-center">
        <h2>Login</h2>

        <?php if (!empty($errorMsg)): ?>
            <div class="error-box"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <form id="login" action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <div class="text-center">
                <button type="submit">Login</button>
            </div>
        </form>
        <p style="margin-top: 15px;">Don't have an account? <a href="registration.php">Register</a></p>
    </section>

    <footer id="footer">
        <div class="containerf">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer>

    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
