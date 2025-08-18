<?php
if (!empty($_GET['mkoa'])) {
  $mkoa = basename($_GET['mkoa']); // Usalama kuzuia path traversal
  $file = __DIR__ . "/csv_files/{$mkoa}.csv";

  if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'Faili ya mkoa haipo']);
    exit;
  }

  $handle = fopen($file, 'r');
  $headers = fgetcsv($handle);
  $data = [];

  while (($row = fgetcsv($handle)) !== false) {
    $rowData = array_combine(array_map('strtolower', $headers), $row);

    $district = $rowData['district'] ?? '';
    $ward = $rowData['ward'] ?? '';
    $place = $rowData['street'] ?: $rowData['places'] ?: '';

    if (!$district || !$ward) continue;

    if (!isset($data[$district])) {
      $data[$district] = [];
    }
    if (!isset($data[$district][$ward])) {
      $data[$district][$ward] = [];
    }
    if ($place && !in_array($place, $data[$district][$ward])) {
      $data[$district][$ward][] = $place;
    }
  }
  fclose($handle);

  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
http_response_code(400);
echo json_encode(['error' => 'Mkoa haijapewa']);
