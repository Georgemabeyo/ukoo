<?php
$host = getenv('DB_HOST');         // Hii itasoma environment variable DB_HOST
$user = getenv('DB_USER');         // Environment variable DB_USER
$password = getenv('DB_PASS');     // Environment variable DB_PASS
$database = getenv('DB_NAME');     // Environment variable DB_NAME
$port = getenv('DB_PORT') ?: 3306; // Optional: default port 3306

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
