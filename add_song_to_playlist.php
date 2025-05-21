<?php
// Mehmet Alphan SARI 20220702127

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

$playlist_id = isset($_GET['playlist_id']) ? intval($_GET['playlist_id']) : 0;
$song_query = isset($_GET['song']) ? trim($_GET['song']) : '';

if ($song_query === '') {
    die("No song name provided.");
}

// search for song
$stmt = $conn->prepare("SELECT song_id, title, duration, genre, image FROM SONGS WHERE title = ?");
$stmt->bind_param("s", $song_query);
$stmt->execute();
$result = $stmt->get_result();
$song = $result->fetch_assoc();

if (!$song) {
    die("Song not found.");
}


$playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;
$song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;

if ($playlist_id > 0 && $song_id > 0) {
    // check if already added
    $check = $conn->prepare("SELECT * FROM PLAYLIST_SONGS WHERE playlist_id = ? AND song_id = ?");
    $check->bind_param("ii", $playlist_id, $song_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO PLAYLIST_SONGS (playlist_id, song_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $playlist_id, $song_id);
        $stmt->execute();
        echo "✅ Song added to playlist.";
    } else {
        echo "⚠️ Song already in playlist.";
    }
} else {
    echo "Invalid request.";
}

echo '<br><a href="playlistpage.php?id=' . $playlist_id . '">← Back to Playlist</a>';
?>
