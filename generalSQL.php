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

// most popular genre
$popularGenreResult = $conn->query("SELECT genre, COUNT(*) as count FROM SONGS GROUP BY genre ORDER BY count DESC LIMIT 1");
$popularGenre = $popularGenreResult->fetch_assoc();

// users by country
$countryStats = [];
$countrySQL = "SELECT C.country_name, COUNT(*) as user_count 
               FROM USERS U 
               JOIN COUNTRY C ON U.country_id = C.country_id 
               GROUP BY C.country_id";
$countryResult = $conn->query($countrySQL);
while ($row = $countryResult->fetch_assoc()) {
    $countryStats[] = $row;
}

// handle genre filter
$genreSongs = [];
if (isset($_GET['genre'])) {
    $selectedGenre = $_GET['genre'];
    $stmt = $conn->prepare("SELECT title, duration FROM SONGS WHERE genre = ? ORDER BY rank DESC LIMIT 5");
    $stmt->bind_param("s", $selectedGenre);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $genreSongs[] = $row;
    }
    $stmt->close();
}

// handle country filter
$topArtists = [];
if (isset($_GET['country_id'])) {
    $selectedCountry = intval($_GET['country_id']);
    $stmt = $conn->prepare("SELECT name, listeners FROM ARTISTS WHERE country_id = ? ORDER BY listeners DESC LIMIT 5");
    $stmt->bind_param("i", $selectedCountry);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $topArtists[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Genre & Country Statistics</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        section { margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Genre & Country Stats</h2>

    <section>
        <h3>Most Popular Genre</h3>
        <p><?php echo htmlspecialchars($popularGenre['genre']) . " with " . $popularGenre['count'] . " songs."; ?></p>
    </section>

    <section>
        <h3>Top 5 Songs in a Genre</h3>
        <form method="get">
            <label>Choose a genre:</label>
            <input type="text" name="genre" required>
            <button type="submit">Get Top Songs</button>
        </form>
        <?php if (!empty($genreSongs)): ?>
            <ul>
                <?php foreach ($genreSongs as $song): ?>
                    <li><?php echo htmlspecialchars($song['title']) . " (" . $song['duration'] . ")"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section>
        <h3>User Count by Country</h3>
        <ul>
            <?php foreach ($countryStats as $stat): ?>
                <li><?php echo htmlspecialchars($stat['country_name']) . ": " . $stat['user_count']; ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h3>Top 5 Artists from a Country</h3>
        <form method="get">
            <label>Enter Country ID:</label>
            <input type="number" name="country_id" required>
            <button type="submit">Get Top Artists</button>
        </form>
        <?php if (!empty($topArtists)): ?>
            <ol>
                <?php foreach ($topArtists as $artist): ?>
                    <li><?php echo htmlspecialchars($artist['name']) . " – " . $artist['listeners'] . " listeners"; ?></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </section>

    <p><a href="homepage.php">← Back to homepage</a></p>
</body>
</html>
