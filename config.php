<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('DB_HOST') ?: 'dpg-d2c7s795pdvs73dcpo6g-a';  
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'ukoo';
$user = getenv('DB_USER') ?: 'makomelelo';
$password = getenv('DB_PASS') ?: 'HeONu12TjSP7NJHeXwMnwdOnzarQ3KvH';

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>
