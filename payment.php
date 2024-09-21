<?php
session_start();

// Check if payment was successful and show success message based on subscription plan
if (isset($_SESSION['payment_success']) && $_SESSION['payment_success']) {
    $subplanID = isset($_SESSION['subplanID']) ? $_SESSION['subplanID'] : 0;
    
    if ($subplanID > 0) {
        echo '<div class="message success">Payment successful!</div>';
    }
    
    unset($_SESSION['payment_success']); // Clear the session variable after displaying
    unset($_SESSION['subplanID']); // Clear the session variable after displaying
}

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

$error = "";
$alreadySubscribed = false; // Initialize $alreadySubscribed

// Handle payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $subplan = isset($_POST['plan']) ? $_POST['plan'] : '';
    $paymenttype = isset($_POST['paymenttype']) ? $_POST['paymenttype'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $userID = $_SESSION['UserID'];

    // Determine the subscription plan ID based on the selected plan
    switch ($subplan) {
        case 'Monthly':
            $subplanID = 1;
            break;
        case 'Yearly':
            $subplanID = 2;
            break;
        default:
            $subplanID = 0; // Default or error case
            break;
    }

    // Fetch user data to check if already subscribed
    $userID = $_SESSION['UserID'];
    $userQuery = "SELECT isSubscribed FROM sereneuser WHERE UserID = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($isSubscribed);
    $stmt->fetch();
    $stmt->close();

    // Initialize isSubscribed and subcheck based on subplan condition
    if ($subplanID > 0 ) {
        $isSubscribed = 1;
        $subcheck = 1;
        $alreadySubscribed = true;
    } else {
        $isSubscribed = 0; // Not subscribed
        $subcheck = 0; // No check
        $alreadySubscribed = false;
    }

    // Update SubscriptionPlan, isSubscribed, UserName, Email, and subcheck in the database
    $updateSubscriptionQuery = "UPDATE sereneuser SET subplan = ?, isSubscribed = ?, UserName = ?, Email = ?, subcheck = ? WHERE UserID = ?";
    $stmt = $conn->prepare($updateSubscriptionQuery);
    $stmt->bind_param("iissii", $subplanID, $isSubscribed, $name, $email, $subcheck, $userID);

    if ($stmt->execute()) {
        // Successful update
        $_SESSION['isSubscribed'] = ($isSubscribed == 1); // Update session variable
        $_SESSION['subcheck'] = ($subcheck == 1); // Update session variable for subcheck

        // Set success message and subplanID in session
        $_SESSION['payment_success'] = true;
        $_SESSION['subplanID'] = $subplanID;

        header("Location: payment.php"); // Redirect to payment.php after update
        exit;
    } else {
        $error = "Error updating SubscriptionPlan: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch user data
$userID = $_SESSION['UserID'];
$userQuery = "SELECT Email, UserName, isSubscribed, subcheck FROM sereneuser WHERE UserID = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($email, $username, $isSubscribed, $subcheck);
$stmt->fetch();
$stmt->close();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Syŕene - Payment</title>
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
        
        .message {
            padding: 10px 20px;
            margin: 10px 0;
            border: 1px solid #4CAF50;
            color: #4CAF50;
            background-color: #E9F5E6;
            border-radius: 4px;
        }

        .message.success {
            border-color: #4CAF50;
            background-color: #E9F5E6;
            color: #4CAF50;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        #payment-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        #payment h2 {
            margin-bottom: 20px;
            color: #343a40;
        }

        #payment label {
            display: block;
            margin-bottom: 10px;
        }

        #payment input[type="text"],
        #payment input[type="email"],
        #payment select {
            width: calc(100% - 20px);
            padding: 8px 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        #payment button {
            background-color: #000;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 93%;
        }

        #payment button:hover {
            background-color: #333;
        }
    </style>
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
                    <li><a class="nav-link scrollto " href="Albums.php">Albums</a></li>
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
    <section id="payment-container" class="d-flex flex-column align-items-center justify-content-center">
        <h2>Payment</h2>

        <?php if ($alreadySubscribed): ?>
            <div class="message error">Already Subscribed!</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="payment" action="payment.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($username); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="subplan">Subscription Plan:</label>
            <select id="subplan" name="plan" required>
                <option value="" disabled selected>Select a subscription plan</option>
                <option value="Monthly">Monthly</option>
                <option value="Yearly">Yearly</option>
            </select>

            <label for="paymenttype">Payment Type:</label>
            <select id="paymenttype" name="paymenttype" required>
                <option value="" disabled selected>Select a payment type</option>
                <option value="Online">Online</option>
                <option value="COD">COD</option>
            </select>

            <input type="hidden" id="subcheck" name="subcheck" value="<?php echo htmlspecialchars($subcheck); ?>">

            <button type="submit" id="submit-button">Proceed</button>
        </form>
    </section>
 <!-- ======= Footer ======= -->
 <footer id="footer">
        <div class="containerf">
            <div class="copyright">
                &copy; Copyright <strong><span>Syŕene</span></strong>. All Rights Reserved
            </div>
            <button class="contact-button" onclick="window.location.href='contact.php';">Contact Us</button>
        </div>
    </footer><!-- End Footer -->
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-button');
            const inputs = document.querySelectorAll('#payment input[required], #payment select[required]');
            const subcheck = document.getElementById('subcheck').value;

            inputs.forEach(input => {
                input.addEventListener('input', checkFormValidity);
            });

            function checkFormValidity() {
                const allFilled = Array.from(inputs).every(input => input.value.trim() !== '');
                submitButton.disabled = !allFilled;
            }

            document.getElementById('payment').addEventListener('submit', function(event) {
                if (subcheck > 0) {
                    event.preventDefault();
                    alert('Already Subscribed!');
                }
            });

            checkFormValidity(); // Initial check
        });
    </script>
</body>
</html>
