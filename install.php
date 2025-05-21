<?php
// Author: Mehmet Alphan SARI 20220702127

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "MehmetAlphanSari_musicplayer";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Drop all tables (dependency-safe order)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS PLAYLIST_SONGS");
$conn->query("DROP TABLE IF EXISTS PLAY_HISTORY");
$conn->query("DROP TABLE IF EXISTS PLAYLISTS");
$conn->query("DROP TABLE IF EXISTS SONGS");
$conn->query("DROP TABLE IF EXISTS ALBUMS");
$conn->query("DROP TABLE IF EXISTS ARTISTS");
$conn->query("DROP TABLE IF EXISTS FOLLOWED_ARTISTS");
$conn->query("DROP TABLE IF EXISTS USERS");
$conn->query("DROP TABLE IF EXISTS COUNTRY");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// COUNTRY
$conn->query("CREATE TABLE COUNTRY (
    country_id INT PRIMARY KEY,
    country_name VARCHAR(100),
    country_code VARCHAR(10)
)");

// USERS
$conn->query("CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT,
    age INT,
    name VARCHAR(100),
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    date_joined DATE,
    last_login DATETIME,
    follower_num INT,
    subscription_type VARCHAR(20),
    top_genre VARCHAR(50),
    num_songs_liked INT,
    most_played_artist VARCHAR(100),
    image VARCHAR(255),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)");

// ARTISTS
$conn->query("CREATE TABLE ARTISTS (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    genre VARCHAR(50),
    date_joined DATE,
    total_num_music INT,
    total_albums INT,
    listeners INT,
    bio TEXT,
    country_id INT,
    image VARCHAR(100),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)");

// ALBUMS
$conn->query("CREATE TABLE ALBUMS (
    album_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT,
    title VARCHAR(100),
    release_date DATE,
    genre VARCHAR(50),
    music_number INT,
    image VARCHAR(100),
    FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id)
)");

// SONGS
$conn->query("CREATE TABLE SONGS (
    song_id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT,
    title VARCHAR(100),
    duration TIME,
    genre VARCHAR(50),
    release_date DATE,
    `rank` FLOAT,
    image VARCHAR(100),
    FOREIGN KEY (album_id) REFERENCES ALBUMS(album_id)
)");

// PLAYLISTS
$conn->query("CREATE TABLE PLAYLISTS (
    playlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100),
    description TEXT,
    date_created DATE,
    image VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)");

// PLAYLIST_SONGS
$conn->query("CREATE TABLE PLAYLIST_SONGS (
    playlistsong_id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT,
    song_id INT,
    date_added DATE,
    FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS(playlist_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)");

// PLAY_HISTORY
$conn->query("CREATE TABLE PLAY_HISTORY (
    play_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    song_id INT,
    playtime DATETIME,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)");

// FOLLOWED_ARTISTS (extra table for follow feature)
$conn->query("CREATE TABLE FOLLOWED_ARTISTS (
    follow_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    artist_id INT,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id),
    FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id)
)");

echo "✅ Database and tables with foreign keys created successfully. Redirecting...";
header("refresh:3;url=login.html");
exit();
?>
