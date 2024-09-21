<?php
session_start();

// Clear session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Update LoggedIn status in database to 0
if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
    
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

    // Update LoggedIn status to 0
    $updateLogoutQuery = "UPDATE sereneuser SET LoggedIn = 0 WHERE UserID = $userID";

    if ($conn->query($updateLogoutQuery) === TRUE) {
        // Success message or logging
        // echo "LoggedIn status updated successfully.";
    } else {
        // Error message or logging
        // echo "Error updating LoggedIn status: " . $conn->error;
    }

    // Close connection
    $conn->close();
}

// Clear session variables again for good measure
$_SESSION = array();

// Ensure all session cookies are deleted
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page after logout
header("Location: login.php");
exit;
?>
