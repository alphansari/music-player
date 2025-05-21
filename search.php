<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "MehmetAlphanSari_musicplayer";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($search === '') {
    die("No search query provided.");
}

// first try searching for a playlist
$stmt = $conn->prepare("SELECT playlist_id FROM PLAYLISTS WHERE title = ?");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $playlist_id = $row['playlist_id'];
    header("Location: playlistpage.php?id=" . $playlist_id);
    exit();
}

// now try searching for a song
$stmt = $conn->prepare("SELECT song_id FROM SONGS WHERE title = ?");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $song_id = $row['song_id'];
    header("Location: songpage.php?id=" . $song_id);
    exit();
}

// not found (not really necessary but I added it anyway)
echo "No matching playlist or song found.";
?>
