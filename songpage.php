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
    die("Connection failed: " . $conn->connect_error);
}

// handle Play (add to PLAY_HISTORY)
$played_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_play'])) {
    $song_id = intval($_POST['song_id']);
    $user_id = $_SESSION['user_id'];

    if ($song_id > 0) {
        $insertPlay = $conn->prepare("INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES (?, ?, NOW())");
        $insertPlay->bind_param("ii", $user_id, $song_id);
        $insertPlay->execute();
        $insertPlay->close();
        $played_message = "🎵 Song has been logged to play history!";
    }
}

// handle Add to Playlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_playlist'])) {
    $playlist_id = intval($_POST['playlist_id']);
    $song_id = intval($_POST['song_id']);

    if ($playlist_id > 0 && $song_id > 0) {
        $check = $conn->prepare("SELECT * FROM PLAYLIST_SONGS WHERE playlist_id = ? AND song_id = ?");
        $check->bind_param("ii", $playlist_id, $song_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            $insert = $conn->prepare("INSERT INTO PLAYLIST_SONGS (playlist_id, song_id) VALUES (?, ?)");
            $insert->bind_param("ii", $playlist_id, $song_id);
            $insert->execute();
            header("Location: songpage.php?id=$song_id&playlist_id=$playlist_id&status=added");
            exit();
        } else {
            header("Location: songpage.php?id=$song_id&playlist_id=$playlist_id&status=exists");
            exit();
        }
    } else {
        die("Missing playlist or song ID.");
    }
}

// fetch song details
$song_id = 0;
$song = null;
$playlist_id = isset($_GET['playlist_id']) ? intval($_GET['playlist_id']) : 0;

if (isset($_GET['id'])) {
    $song_id = intval($_GET['id']);
    $query = "SELECT S.song_id, S.title, S.duration, S.genre, S.image, A.title AS album_name
              FROM SONGS S
              JOIN ALBUMS A ON S.album_id = A.album_id
              WHERE S.song_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    $stmt->close();
} elseif (isset($_GET['song'])) {
    $song_title = trim($_GET['song']);
    if ($song_title !== "") {
        $query = "SELECT S.song_id, S.title, S.duration, S.genre, S.image, A.title AS album_name
                  FROM SONGS S
                  JOIN ALBUMS A ON S.album_id = A.album_id
                  WHERE S.title = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $song_title);
        $stmt->execute();
        $result = $stmt->get_result();
        $song = $result->fetch_assoc();
        $stmt->close();
        if ($song) {
            $song_id = $song['song_id'];
        }
    }
}

if (!$song) {
    die("Song not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($song['title']); ?> - Song Page</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }

        .song-box {
            max-width: 40px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        img {
            width: 10%;
            height: auto;
            border-radius: 8px;
        }

        h2 {
            margin-bottom: 10px;
            color: #8e0505;
        }

        p {
            margin: 8px 0;
        }

        button {
            background-color: #8e0505;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #6c0404;
        }

        .message {
            color: green;
            margin 10px 0;
        }
    </style>
</head>
<body>
    <class="song-box">
        <h2><?php echo htmlspecialchars($song['title']); ?></h2>
        <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="Album Art">
        <p><strong>Album:</strong> <?php echo htmlspecialchars($song['album_name']); ?></p>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($song['genre']); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($song['duration']); ?></p>

        <form method="post">
            <input type="hidden" name="log_play" value="1">
            <input type="hidden" name="song_id" value="<?php echo $song_id; ?>">
            <button type="submit">▶ Play</button>
        </form>

        <?php if (!empty($played_message)): ?>
            <p class="message"><?php echo $played_message; ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['status'])): ?>
            <p class="message">
                <?php 
                if ($_GET['status'] === 'added') echo "✅ Song added to playlist!";
                elseif ($_GET['status'] === 'exists') echo "⚠️ Song already in playlist.";
                ?>
            </p>
        <?php endif; ?>

        <?php if ($playlist_id > 0): ?>
            <form action="songpage.php" method="post">
                <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
                <input type="hidden" name="song_id" value="<?php echo $song_id; ?>">
                <button type="submit" name="add_to_playlist">Add to this Playlist</button>
            </form>
        <?php endif; ?>

        <p><a href="homepage.php">← Back to homepage</a></p>
    </div>
</body>
</html>
