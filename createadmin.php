<?php
// admin_create.php - tumia mara moja tu ku-create admin

include 'config.php';

$username = 'admin';  
$password = 'admin123'; // Badilisha password hii

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES ($1, $2)";
$result = pg_query_params($conn, $sql, [$username, $hashed_password]);

if ($result) {
    echo "Admin account imeundwa!";
} else {
    echo "Tatizo: " . pg_last_error($conn);
}
?>
