<?php
// Mehmet Alphan SARI

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
    die("Database connection failed: " . $conn->connect_error);
}

$song_title = isset($_GET['song']) ? trim($_GET['song']) : '';

if ($song_title === '') {
    die("Song title missing.");
}

// Lookup song by title
$stmt = $conn->prepare("SELECT song_id FROM SONGS WHERE title = ?");
$stmt->bind_param("s", $song_title);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $song_id = $row['song_id'];
    // Redirect using the ID, which is more reliable
    header("Location: songpage.php?id=" . $song_id);
    exit();
} else {
    echo "Song not found.";
}
?>
