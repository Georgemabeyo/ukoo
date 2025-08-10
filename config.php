<?php
// Onyesha errors wakati wa development (unaweza kuzima production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Soma credentials kutoka Environment Variables za Render
$host = getenv('DB_HOST') ?: 'dpg-d2c7s795pdvs73dcpo6g-a';
$user = getenv('DB_USER') ?: 'makomelelo';
$pass = getenv('DB_PASS') ?: 'HeONu12TjSP7NJHeXwMnwdOnzarQ3KvH';
$dbname = getenv('DB_NAME') ?: 'ukoo';
$port = getenv('DB_PORT') ?: 5432;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Kagua connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
