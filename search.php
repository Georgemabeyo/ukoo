<?php
// search.php - PostgreSQL search for persons

header('Content-Type: application/json; charset=utf-8');

$host = 'dpg-d2c7s795pdvs73dcpo6g-a';
$port = 5432;
$dbname = 'ukoo';
$user = 'makomelelo';
$pass = 'HeONu12TjSP7NJHeXwMnwdOnzarQ3KvH';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo json_encode([]);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Badilisha jina la jedwali na safu kama ilivyo kwenye database yako
$sql = "SELECT id, full_name, village, ward, region, photo_url 
        FROM persons 
        WHERE full_name ILIKE :search 
        ORDER BY full_name ASC 
        LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$q%"]);

$results = $stmt->fetchAll();

echo json_encode($results);
