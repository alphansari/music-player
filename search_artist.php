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
    die("Database connection failed: " . $conn->connect_error);
}

$artist_name = isset($_GET['artist']) ? trim($_GET['artist']) : '';
if ($artist_name === '') {
    die("Please enter an artist name.");
}

$stmt = $conn->prepare("SELECT artist_id FROM ARTISTS WHERE name = ?");
$stmt->bind_param("s", $artist_name);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $artist_id = $row['artist_id'];
    header("Location: artistpage.php?id=$artist_id");
    exit();
} else {
    echo "Artist not found. <a href='homepage.php'>Go back</a>";
}
?>
