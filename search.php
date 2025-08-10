<?php
header('Content-Type: application/json; charset=utf-8');

include 'config.php'; // Hapa iwepo maelezo ya DB connection

// Connect to DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Prepare SQL with LIKE wildcard, use prepared statements for security
$sql = "SELECT id, full_name, photo_url, village, ward, region FROM people WHERE full_name LIKE ? LIMIT 15";
$stmt = $conn->prepare($sql);
$searchParam = "%{$q}%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();

$result = $stmt->get_result();

$people = [];
while ($row = $result->fetch_assoc()) {
    $people[] = [
        "id" => $row['id'],
        "full_name" => $row['full_name'],
        "photo_url" => $row['photo_url'],
        "village" => $row['village'],
        "ward" => $row['ward'],
        "region" => $row['region']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($people);
exit;
?>
