<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credentials zako kutoka Render au default local values
$host = getenv('DB_HOST') ?: 'dpg-d2c7s795pdvs73dcpo6g-a';  // Host kamili wa PostgreSQL
$port = getenv('DB_PORT') ?: '5432';                          // Port ya PostgreSQL
$dbname = getenv('DB_NAME') ?: 'ukoo';                        // Jina la database
$user = getenv('DB_USER') ?: 'makomelelo';                    // Jina la user
$password = getenv('DB_PASS') ?: 'HeONu12TjSP7NJHeXwMnwdOnzarQ3KvH'; // Password

// Unganisha PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>
