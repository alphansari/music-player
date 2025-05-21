<?php
// Mehmet Alphan SARI 20220702127

$names = file("names.txt", FILE_IGNORE_NEW_LINES);
if (!$names) die("names.txt not found");

function randomName($names) {
    return $names[array_rand($names)];
}

function randDate($start = "-1000 days") {
    return date("Y-m-d", strtotime($start));
}

function randDateTime() {
    return date("Y-m-d H:i:s", strtotime("-" . rand(1, 1000) . " hours"));
}

// COUNTRY
$country_output = fopen("insert_countries.sql", "w");
$country_names = ["Türkiye", "Germany", "USA", "Canada", "France", "Italy", "Japan", "India", "Brazil", "UK"];
foreach ($country_names as $i => $name) {
    $code = strtoupper(substr($name, 0, 2));
    $id = $i + 1;
    fwrite($country_output, "INSERT INTO COUNTRY (country_id, country_name, country_code) VALUES ($id, '$name', '$code');\n");
}
fclose($country_output);

// USERS
$user_output = fopen("insert_users.sql", "w");
for ($i = 1; $i <= 100; $i++) {
    $name = randomName($names);
    $username = strtolower($name) . rand(100, 999);
    $email = "$username@example.com";
    $password = "pass" . rand(1000, 9999);
    $age = rand(18, 60);
    $country_id = rand(1, 10);
    $follower = rand(0, 1000);
    $sub = rand(0, 1) ? 'free' : 'premium';
    $genre = 'Pop';
    $liked = rand(0, 200);
    $mp_artist = 'Placeholder Artist';
    $date_joined = randDate();
    $last_login = randDateTime();
    fwrite($user_output, "INSERT INTO USERS (country_id, age, name, username, email, password, date_joined, last_login, follower_num, subscription_type, top_genre, num_songs_liked, most_played_artist, image) VALUES 
        ($country_id, $age, '$name', '$username', '$email', '$password', '$date_joined', '$last_login', $follower, '$sub', '$genre', $liked, '$mp_artist', 'profile.jpg');\n");
}
fclose($user_output);

// ARTISTS
$artist_output = fopen("insert_artists.sql", "w");
for ($i = 1; $i <= 100; $i++) {
    $name = "Artist $i";
    $genre = "Pop";
    $date_joined = randDate();
    $music_num = rand(1, 100);
    $album_num = rand(1, 20);
    $listeners = rand(100, 10000);
    $bio = "Bio of artist $i.";
    $country_id = rand(1, 10);
    fwrite($artist_output, "INSERT INTO ARTISTS (name, genre, date_joined, total_num_music, total_albums, listeners, bio, country_id, image) VALUES 
        ('$name', '$genre', '$date_joined', $music_num, $album_num, $listeners, '$bio', $country_id, 'artist.jpg');\n");
}
fclose($artist_output);

// ALBUMS
$album_output = fopen("insert_albums.sql", "w");
for ($i = 1; $i <= 200; $i++) {
    $artist_id = rand(1, 100);
    $title = "Album $i";
    $release = randDate();
    $genre = "Pop";
    $music_number = rand(5, 15);
    fwrite($album_output, "INSERT INTO ALBUMS (artist_id, title, release_date, genre, music_number, image) VALUES 
        ($artist_id, '$title', '$release', '$genre', $music_number, 'album.jpg');\n");
}
fclose($album_output);

// SONGS
$song_output = fopen("insert_songs.sql", "w");
for ($i = 1; $i <= 500; $i++) {
    $album_id = rand(1, 200);
    $title = "Song $i";
    $duration = sprintf("00:%02d:%02d", rand(1, 4), rand(0, 59));
    $genre = "Pop";
    $release = randDate();
    $rank = rand(1, 100) / 10.0;
    fwrite($song_output, "INSERT INTO SONGS (album_id, title, duration, genre, release_date, `rank`, image) VALUES 
        ($album_id, '$title', '$duration', '$genre', '$release', $rank, 'song.jpg');\n");
}
fclose($song_output);

// PLAYLISTS
$playlist_output = fopen("insert_playlists.sql", "w");
for ($i = 1; $i <= 150; $i++) {
    $user_id = rand(1, 100);
    $title = "Playlist $i";
    $desc = "This is playlist $i.";
    $date_created = randDate();
    fwrite($playlist_output, "INSERT INTO PLAYLISTS (user_id, title, description, date_created, image) VALUES 
        ($user_id, '$title', '$desc', '$date_created', 'playlist.jpg');\n");
}
fclose($playlist_output);

// PLAYLIST_SONGS
$plsongs_output = fopen("insert_playlistsongs.sql", "w");
for ($i = 1; $i <= 300; $i++) {
    $playlist_id = rand(1, 150);
    $song_id = rand(1, 500);
    $date_added = randDate();
    fwrite($plsongs_output, "INSERT INTO PLAYLIST_SONGS (playlist_id, song_id, date_added) VALUES 
        ($playlist_id, $song_id, '$date_added');\n");
}
fclose($plsongs_output);

// PLAY_HISTORY
$history_output = fopen("insert_history.sql", "w");
for ($i = 1; $i <= 500; $i++) {
    $user_id = rand(1, 100);
    $song_id = rand(1, 500);
    $playtime = randDateTime();
    fwrite($history_output, "INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES 
        ($user_id, $song_id, '$playtime');\n");
}
fclose($history_output);

echo "SQL data files generated successfully.";
?>
