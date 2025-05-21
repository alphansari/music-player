<?php
// Mehmet Alphan SARI 20220702127

session_start();

if (!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "MehmetAlphanSari_musicplayer";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT S.title, S.duration, S.genre, S.image, P.playtime
        FROM PLAY_HISTORY P
        JOIN SONGS S ON P.song_id = S.song_id
        WHERE P.user_id = ?
        ORDER BY P.playtime DESC
        LIMIT 1";  
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$currentSong = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Now Playing</title>
        <style>
            body { font-family: Arial; padding: 20px; }
            .song-box { border: 1px solid #ccc; padding: 20px; width: 300px; }
            img { width: 100%; height: auto; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <h2>Currently Playing</h2>

        <?php if ($currentSong): ?>
            <div class="song-box">
                <img src="<?php echo htmlspecialchars($currentSong['image']); ?>" alt="Album Art">
                <h3><?php echo htmlspecialchars($currentSong['title']); ?></h3>
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($currentSong['genre']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($currentSong['duration']); ?></p>
                <p><strong>Last Played:</strong> <?php echo htmlspecialchars($currentSong['playtime']); ?></p>
            </div>
        <?php else: ?>
            <p>No recent music played.</p>
        <?php endif; ?>

        <p><a href="homepage.php">← Back to homepage</a></p>
    </body>
</html>