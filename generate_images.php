<?php
// Mehmet Alphan SARI 20220702127

$outputFile = 'update_images.sql';
$out = fopen($outputFile, 'w');

if (!$out) {
    die("Failed to open output file.");
}

// Total number of records
$totalArtists = 200;
$totalAlbums = 200;
$totalSongs = 1000;
$totalPlaylists = 500;

// Generate for ARTISTS
for ($i = 1; $i <= $totalArtists; $i++) {
    $img = "https://picsum.photos/seed/artist{$i}/200/200";
    fwrite($out, "UPDATE ARTISTS SET image = '$img' WHERE artist_id = $i;\n");
}

// Generate for ALBUMS
for ($i = 1; $i <= $totalAlbums; $i++) {
    $img = "https://placehold.co/300x300?text=Album+{$i}";
    fwrite($out, "UPDATE ALBUMS SET image = '$img' WHERE album_id = $i;\n");
}

// Generate for SONGS
for ($i = 1; $i <= $totalSongs; $i++) {
    $img = "https://placehold.co/300x300?text=Song+{$i}";
    fwrite($out, "UPDATE SONGS SET image = '$img' WHERE song_id = $i;\n");
}

// Generate for PLAYLISTS
for ($i = 1; $i <= $totalPlaylists; $i++) {
    $img = "https://picsum.photos/seed/artist{$i}/500/500";
    fwrite($out, "UPDATE PLAYLISTS SET image = '$img' WHERE playlist_id = $i;\n");
}

fclose($out);
echo "UPDATE queries written to $outputFile\n";
?>
