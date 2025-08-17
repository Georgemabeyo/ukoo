<?php
include 'config.php';
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>Hakuna ID iliyotumwa.</p>";
    exit;
}

$member_id = (int)$_GET['id'];
$sql = "SELECT m.id, m.first_name AS jina, m.phone AS simuna, m.photo AS picha, m.parent_id, 
        p.first_name || ' ' || p.last_name AS jina_mzazi
        FROM family_tree m
        LEFT JOIN family_tree p ON m.parent_id = p.id
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
$picha = !empty($row['picha']) ? "uploads/" . htmlspecialchars($row['picha']) : "uploads/default.png";
$mzazi = !empty($row['jina_mzazi']) ? htmlspecialchars($row['jina_mzazi']) : "Hakuna";

echo "<div style='text-align:center;'>
        <img src='" . $picha . "' alt='Picha ya " . htmlspecialchars($row['jina']) . "' 
             style='width:120px; height:120px; border-radius:50%; border:3px solid #ffc107; margin-bottom:10px;'>
        <h3 style='color:#0d47a1;'>" . htmlspecialchars($row['jina']) . "</h3>
        <p><strong>Namba ya simu:</strong> " . htmlspecialchars($row['simuna']) . "</p>
        <p><strong>Mzazi:</strong> " . $mzazi . "</p>";

// Ruhusa kuonesha button ya edit
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

if ($user_id !== null) {
    // Ruhusa ya ku-edit mtu mwenyewe au admin tu
    if ($user_role === 'admin' || $user_id === $row['id']) {
        echo "<p><a href='edit_member.php?id=" . $row['id'] . "' class='btn-edit' style='display:inline-block; padding:8px 15px; background:#ffc107; color:#000; border-radius:5px; text-decoration:none; margin-top:10px;'>Hariri Taarifa</a></p>";
    }
}

echo "</div>";

pg_free_result($result);
?>
