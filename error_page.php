 n    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "newserene";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $songID = $_POST['song_id'];

    $sql = "DELETE FROM Song WHERE SongID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $songID); // Assuming SongID is an integer
    
    if ($stmt->execute() === false) {
        header("Location: confirmation_page.php?error=true");
        exit();
    }

    $stmt->close();
    $conn->close();

    header("Location: add_song.php");
    exit();
} else {
    
    header("Location: confirmation_page.php?error=true");
    exit();
}
?>
