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
        $sql = "DELETE FROM artist WHERE ArtistID = ?";
        $redirect_page = isset($_POST['origin_page']) ? $_POST['origin_page'] : "Artists.php";
    } elseif (isset($_POST['album_id'])) {
        // Delete from album table and associated songs
        $albumID = $_POST['album_id'];

        // First, delete associated songs (if any) from song table
        $delete_songs_sql = "DELETE FROM song WHERE AlbumID = ?";
        $stmt_delete_songs = $conn->prepare($delete_songs_sql);
        $stmt_delete_songs->bind_param("i", $albumID);

        if ($stmt_delete_songs->execute() === false) {
            die("Error deleting songs: " . $stmt_delete_songs->error);
        }

        $stmt_delete_songs->close();

        // Now delete the album itself from album table
        $sql = "DELETE FROM album WHERE AlbumID = ?";
        $redirect_page = isset($_POST['origin_page']) ? $_POST['origin_page'] : "Albums.php";
    } elseif (isset($_POST['song_id'])) {
        // Delete from song table
        $songID = $_POST['song_id'];
        $sql = "DELETE FROM song WHERE SongID = ?";
        $redirect_page = isset($_POST['origin_page']) ? $_POST['origin_page'] : "Songs.php";
    } else {
        // Invalid delete request
        header("Location: error_page.php");
        exit();
    }

    // Prepare and execute the delete statement
    $stmt = $conn->prepare($sql);

    if (isset($artistID)) {
        $stmt->bind_param("i", $artistID);
    } elseif (isset($albumID)) {
        $stmt->bind_param("i", $albumID);
    } elseif (isset($songID)) {
        $stmt->bind_param("i", $songID);
    }

    if ($stmt->execute() === false) {
        die("Error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the originating page after successful deletion
    header("Location: Album_List.php");
    exit();
} else {
    // Redirect to error page for invalid request method
    header("Location: error_page.php");
    exit();
}
?>
