<?php
header('Content-Type: application/json');
include 'config.php'; // database connection

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = $_GET['q'];
$q = $conn->real_escape_string($q);

$sql = "SELECT id, full_name, photo, age, region, town, phone, email, marital_status, children FROM family_tree WHERE full_name LIKE '%$q%' LIMIT 10";
$result = $conn->query($sql);

$people = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $people[] = [
            'id' => $row['id'],
            'full_name' => $row['full_name'],
            'photo' => $row['photo'], // Make sure this is a URL or relative path to image
            'age' => $row['age'],
            'region' => $row['region'],
            'town' => $row['town'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'marital_status' => $row['marital_status'],
            'children' => $row['children'],
        ];
    }
}

echo json_encode($people);
