<?php
// view_member.php
include 'config.php'; // Hapa kuna DB connection yako ya PostgreSQL, inapaswa kuwa $conn

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>Hakuna ID iliyotumwa.</p>";
    exit;
}

$member_id = (int)$_GET['id'];

// Tumia prepared statement kwa PostgreSQL
$sql = "SELECT m.id, m.jina, m.simuna, m.picha, m.parent_id, p.jina AS jina_mzazi
        FROM members m
        LEFT JOIN members p ON m.parent_id = p.id
        WHERE m.id = $1";

$result = pg_query_params($conn, $sql, [$member_id]);

if (!$result) {
    echo "<p style='color:red; text-align:center;'>Tatizo la query: " . pg_last_error($conn) . "</p>";
    exit;
}

if (pg_num_rows($result) === 0) {
    echo "<p style='color:red; text-align:center;'>Taarifa hazijapatikana.</p>";
    exit;
}

$row = pg_fetch_assoc($result);

// Picha
$picha = !empty($row['picha']) ? "uploads/" . htmlspecialchars($row['picha']) : "uploads/default.png";

// Mzazi
$mzazi = !empty($row['jina_mzazi']) ? htmlspecialchars($row['jina_mzazi']) : "Hakuna";

// Output HTML
echo "<div style='text-align:center;'>
        <img src='" . $picha . "' alt='Picha ya " . htmlspecialchars($row['jina']) . "' 
             style='width:120px; height:120px; border-radius:50%; border:3px solid #ffc107; margin-bottom:10px;'>
        <h3 style='color:#0d47a1;'>" . htmlspecialchars($row['jina']) . "</h3>
        <p style='color:#333;'><strong>Namba ya simu:</strong> " . htmlspecialchars($row['simuna']) . "</p>
        <p style='color:#333;'><strong>Mzazi:</strong> " . $mzazi . "</p>
      </div>";

pg_free_result($result);
?>
