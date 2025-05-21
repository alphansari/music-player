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

$artist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($artist_id <= 0) {
    die("Invalid artist ID.");
}

// fetch artist details
$stmt1 = $conn->prepare("SELECT name, genre, bio, image FROM ARTISTS WHERE artist_id = ?");
$stmt1->bind_param("i", $artist_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$artist = $result1->fetch_assoc();
$stmt1->close();

if (!$artist) {
    die("Artist not found.");
}

// fetch artist's albums
$albums = [];
$stmt2 = $conn->prepare("SELECT title, release_date, image FROM ALBUMS WHERE artist_id = ?");
$stmt2->bind_param("i", $artist_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    $albums[] = $row;
}
$stmt2->close();

// fetch top 5 songs
$songs = [];
$stmt3 = $conn->prepare("SELECT title, duration, `rank` FROM SONGS WHERE album_id IN (SELECT album_id FROM ALBUMS WHERE artist_id = ?) ORDER BY `rank` DESC LIMIT 5");
$stmt3->bind_param("i", $artist_id);
$stmt3->execute();
$result3 = $stmt3->get_result();
while ($row = $result3->fetch_assoc()) {
    $songs[] = $row;
}
$stmt3->close();

// follow artist if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['follow'])) {
    $check = $conn->prepare("SELECT * FROM FOLLOWED_ARTISTS WHERE user_id = ? AND artist_id = ?");
    $check->bind_param("ii", $_SESSION['user_id'], $artist_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO FOLLOWED_ARTISTS (user_id, artist_id) VALUES (?, ?)");
        $insert->bind_param("ii", $_SESSION['user_id'], $artist_id);
        $insert->execute();
        echo "<p>You are now following this artist.</p>";
    } else {
        echo "<p>You are already following this artist.</p>";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($artist['name']); ?> - Artist Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f5f5f5;
            color: #333;
        }

        h2, h3 {
            color: #8e0505;
        }

        .artist-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .artist-box img {
            width: 200px;
            height: auto;
            border-radius: 10px;
            margin-right: 20px;
        }

        .artist-details p {
            margin: 5px 0;
        }

        .album, .song {
            margin 10px 0;
        }

        .album img {
            width: 80px;
            height: 80px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .follow-form {
            margin-top: 20px;
        }

        .follow-form button {
            background-color: #a51919;
        }

        a {
            text-decoration: none;
            color: #8e0505
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2><?php echo htmlspecialchars($artist['name']); ?></h2>

    <div class="artist-box">
        <img src="<?php echo htmlspecialchars($artist['image']); ?>" alt="Artist Image">
        <div class="artist-details">
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($artist['genre']); ?></p>
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($artist['bio']); ?></p>
        </div>
    </div>


    <h3>Albums</h3>
    <ul>
        <?php foreach ($albums as $album): ?>
            <li class="album">
                <img src="<?php echo htmlspecialchars($album['image']); ?>" alt="Album Cover">
                <span><?php echo htmlspecialchars($album['title']); ?> (<?php echo htmlspecialchars($album['release_date']); ?>)</span>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Top 5 Songs</h3>
    <ol>
        <?php foreach ($songs as $song): ?>
            <li>
                <?php echo htmlspecialchars($song['title']); ?> – 
                <?php echo htmlspecialchars($song['duration']); ?> 
                (Rank: <?php echo htmlspecialchars($song['rank']); ?>)
            </li>
        <?php endforeach; ?>
    </ol>

    <p><a href="homepage.php">← Back to homepage</a></p>
</body>
</html>
