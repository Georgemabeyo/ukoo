<?php
// Onyesha errors wakati wa development (toa hizi lines kwenye production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credentials za PostgreSQL
$host     = "dpg-d2c7s795pdvs73dcpo6g-a.oregon-postgres.render.com";
$port     = "5432";
$dbname   = "ukoo";
$user     = "makomelelo";
$password = "HeONu12TjSP7NJHeXwMnwdOnzarQ3KvH";
$sslmode  = "require"; // Render mara nyingi inahitaji SSL

// Unganisha kwenye PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=$sslmode";
$conn = pg_connect($conn_string);

// Angalia kama connection imefanikiwa
if (!$conn) {
    die("❌ Connection failed: " . pg_last_error());
}

// Optional: Set UTF8
pg_set_client_encoding($conn, "UTF8");
?>
