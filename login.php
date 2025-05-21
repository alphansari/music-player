
<?php
// Mehmet Alphan SARI 20220702127
session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "MehmetAlphanSari_musicplayer"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM USERS WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $user, $pass);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];

            header("Location: homepage.html");
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Execution failed: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Please enter both username and password.";
}

$conn->close();
?>
