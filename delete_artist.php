<?php
session_start(); // Start session if not already started

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "newserene";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check where the delete request is coming from
    if (isset($_POST['artist_id'])) {
        // Delete from artist table
        $artistID = $_POST['artist_id'];
        $redirect_page = isset($_POST['origin_page']) ? $_POST['origin_page'] : "Artists.php";

        // First, delete associated albums (and their songs) from album table
        $delete_albums_sql = "DELETE a, s FROM album a
                              LEFT JOIN song s ON a.AlbumID = s.AlbumID
                              WHERE a.ArtistID = ?";
        $stmt_delete_albums = $conn->prepare($delete_albums_sql);
        $stmt_delete_albums->bind_param("i", $artistID);

        if ($stmt_delete_albums->execute() === false) {
            die("Error deleting albums: " . $stmt_delete_albums->error);
        }

        $stmt_delete_albums->close();

        // Now delete the artist itself from artist table
        $sql = "DELETE FROM artist WHERE ArtistID = ?";
        
        // Prepare and execute the delete statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $artistID);

        if ($stmt->execute() === false) {
            die("Error deleting artist: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirect back to the originating page after successful deletion
        header("Location: Artist_List.php");
        exit();
    } else {
        // Invalid delete request
        header("Location: error_page.php");
        exit();
    }
} else {
    // Redirect to error page for invalid request method
    header("Location: error_page.php");
    exit();
}
?>

