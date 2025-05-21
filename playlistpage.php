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

// Get playlist ID
$playlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($playlist_id <= 0) {
    die("Invalid playlist ID.");
}

// Handle play action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['play_song'])) {
    $played_song_id = intval($_POST['song_id']);
    $user_id = $_SESSION['user_id'];
    $playtime = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $played_song_id, $playtime);
    $stmt->execute();

    // Avoid resubmission
    header("Location: playlistpage.php?id=$playlist_id");
    exit();
}

// Fetch playlist title
$title = "Unknown Playlist";
$image = "default_playlist.jpg";

$stmt = $conn->prepare("SELECT title, image FROM PLAYLISTS WHERE playlist_id = ?");
$stmt->bind_param("i", $playlist_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $title = $row['title'];
    $image = $row['image'];
}
$stmt->close();

// Fetch songs in playlist
$songs = [];
$sql = "SELECT S.song_id, S.title, S.duration, S.genre, S.image, C.country_name
        FROM PLAYLIST_SONGS PS
        JOIN SONGS S ON PS.song_id = S.song_id
        JOIN ALBUMS A ON S.album_id = A.album_id
        JOIN ARTISTS AR ON A.artist_id = AR.artist_id
        JOIN COUNTRY C ON AR.country_id = C.country_id
        WHERE PS.playlist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playlist_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $songs[] = $row;
}
$stmt->close();


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?> - Playlist</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f9f9f9; }
        h2 { margin-bottom: 20px; }
        ul { list-style: none; padding: 0; }
        .song { margin-bottom: 15px; background: #fff; padding: 10px; border-radius: 5px; display: flex; align-items: center; }
        img { width: 60px; height: 60px; margin-right: 10px; border-radius: 4px; }
        .details { flex-grow: 1; }
        button { background-color: #8e0505; color: white; border: none; padding: 6px 10px; cursor: pointer; border-radius: 4px; }
        form.inline { display: inline; margin-left: 10px; }
    </style>
</head>
<body>
    <h2>Playlist: <?php echo htmlspecialchars($title); ?></h2>
    <img src="<?php echo htmlspecialchars($image); ?>" alt="Playlist Image" style="width:200px; height: auto; margin-bottom:20px;">
    <form action="songpage.php" method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
        <input type="text" name="song" placeholder="Search song to add">
        <button type="submit">Search & Add</button>
    </form>

    <?php if (empty($songs)): ?>
        <p>No songs in this playlist.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($songs as $song): ?>
                <li class="song">
                    <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="cover">
                    <div class="details">
                        <strong><?php echo htmlspecialchars($song['title']); ?></strong><br>
                        (<?php echo htmlspecialchars($song['duration']); ?>, <?php echo htmlspecialchars($song['genre']); ?>, Country: <?php echo htmlspecialchars($song['country_name']); ?>)
                    </div>
                    <form method="post" class="inline">
                        <input type="hidden" name="play_song" value="1">
                        <input type="hidden" name="song_id" value="<?php echo $song['song_id']; ?>">
                        <button type="submit">▶ Play</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="homepage.php">← Back to homepage</a></p>
</body>
</html>
