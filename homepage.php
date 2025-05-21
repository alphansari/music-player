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

if ($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$playlists = []; 
// $playlists_sql = "SELECT title, image FROM PLAYLISTS WHERE user_id = ?";
$playlists_sql = "SELECT playlist_id, title, image FROM PLAYLISTS WHERE user_id = ?";
$stmt = $conn->prepare($playlists_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $playlists[] = $row;
}

$lastPlayed = [];
$history_sql = "SELECT S.title FROM PLAY_HISTORY P
                JOIN SONGS S ON P.song_id = S.song_id
                WHERE P.user_id = ?
                ORDER BY P.playtime DESC
                LIMIT 10";
$stmt = $conn->prepare($history_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $lastPlayed[] = $row['title'];
}

// we fetch the user's country_id here
$country_sql = "SELECT country_id FROM USERS WHERE user_id = ?";
$stmt = $conn->prepare($country_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_country_id = $result->fetch_assoc()['country_id'];

// then we fetch the artists
$topArtists = [];
$artist_sql = "SELECT name FROM ARTISTS WHERE country_id = ? ORDER BY listeners DESC LIMIT 5";
$stmt = $conn->prepare($artist_sql);
$stmt->bind_param("i", $user_country_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $topArtists[] = $row['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Hello, <?php echo htmlspecialchars($name); ?>!</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                margin: 0;
                display: flex;
                min-height: 100vh;
                background #f4f4f9;
            }

            .sidebar, .content, .artists {
                padding: 20px;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                margin: 20px;
                border-radius: 10px;
            }

            .sidebar { width: 20%; }
            .content { width: 50%; }
            .artists { width: 25%; }

            h3 {
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
            }

            ul, ol{
                list-style: none;
                padding: 0;
            }

            li {
                margin: 10px 0;
            }

            img {
                border-radius: 5px;
                margin-right: 10px;
            }

            input[type="text"] {
                padding: 8px;
                width: 80%;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            button {
                padding: 8px 12px;
                background-color: #8e0505;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background-color: #8e0505;
            }

            .search-box {
                margin-bottom: 15px;
            }

            .add-button {
                margin-top: 10px;
                width: 100%;
                background-color: #007BFF;
            }

            .add-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <div class="search-box">
                <form action="search.php" method="get">
                    <input type="text" name="query" placeholder="Search playlists or songs">
                    <button type="submit"><Search></button>
                </form>
            </div>
            <h3>Your Playlists</h3>
            <ul>
                <?php foreach ($playlists as $pl): ?>
                    <li>
                        <img src="<?php echo htmlspecialchars($pl['image']); ?>" alt="Cover" width="50" height="50">
                        <a href="playlistpage.php?id=<?php echo $pl['playlist_id']; ?>">
                            <?php echo htmlspecialchars($pl['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form action="add_playlist.php" method="get">
                <button type="submit">+ Add Playlist</button>
            </form>
        </div>

        <div class="content">
            <h3>Last 10 Played Songs</h3>
            <ol>
                <?php foreach ($lastPlayed as $song): ?>
                    <li><?php echo htmlspecialchars($song); ?></li>
                <?php endforeach; ?>
            </ol>
            <div class="search-box">
                <form action="search_song.php" method="get">
                    <input type="text" name="song" placeholder="Search song from history">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <div class="artists">
            <div class="search-box">
                <form action="search_artist.php" method="get">
                    <input type="text" name="artist"placeholder="Search artist">
                    <button type="submit">Search</button>
                </form>
            </div>
            <h3>Top 5 Artists from your Country</h3>
            <ul>
                <?php foreach ($topArtists as $artist): ?>
                    <li><?php echo htmlspecialchars($artist); ?></html></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </body>
</html>