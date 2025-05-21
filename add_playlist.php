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

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $user_id = $_SESSION['user_id'];

    if ($title !== "") {
        $stmt = $conn->prepare("INSERT INTO PLAYLISTS (title, description, image, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $image, $user_id);
        if ($stmt->execute()){
            $success_message = "✅ Playlist created successfully!";
        } else {
            $error_message = "❌ Failed to create playlist.";
        }
        $stmt->close();
    } else {
        $error_message = "!!! Title is required.";
    }

    // $stmt = $conn->prepare("INSERT INTO PLAYLISTS (user_id, title, description, date_created, image) VALUES (?, ?, ?, NOW(), ?)");
    // $stmt->bind_param("isss", $user_id, $title, $description, $image);

    // if ($stmt->execute()) {
    //     $message = "Playlist created successfully.";
    // } else {
    //     $message = "Error: " . $stmt->error;
    // }
    // $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Playlist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f8f8f8;
            max-width: 500px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #8e0505;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #8e0505;
            color: white;
            padding: 12px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #6e0404;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #8e0505;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Create New Playlist</h2>
    <?php if ($success_message): ?>
        <p class="message" style="color: green;"><?php echo $success_message; ?></p>
    <?php elseif ($error_message): ?>
        <p class="message" style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="title">Playlist Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="image">Image URL:</label>
        <input type="text" name="image" id="image">

        <button type="submit">Create Playlist</button>
    </form>

    <div class="back-link">
        <a href="homepage.php">← Back to homepage</a>
    </div>
</body>
</html>
