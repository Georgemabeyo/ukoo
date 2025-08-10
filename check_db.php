<?php
$host = getenv('DB_HOST'); // environment variable
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_array()) {
        echo $row[0]."<br>";
    }
} else {
    echo "No tables found";
}

$conn->close();
?>
