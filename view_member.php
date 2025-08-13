<?php
// view_member.php
include 'config.php'; // Hapa kuna DB connection yako

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>Hakuna ID iliyotumwa.</p>";
    exit;
}

$member_id = intval($_GET['id']);

// Pata taarifa za member
$sql = "SELECT m.id, m.jina, m.simuna, m.picha, m.parent_id, p.jina AS jina_mzazi
        FROM members m
        LEFT JOIN members p ON m.parent_id = p.id
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red; text-align:center;'>Taarifa hazijapatikana.</p>";
    exit;
}

$row = $result->fetch_assoc();

// Picha
$picha = !empty($row['picha']) ? "uploads/" . $row['picha'] : "uploads/default.png";

// Mzazi
$mzazi = !empty($row['jina_mzazi']) ? $row['jina_mzazi'] : "Hakuna";

// Output HTML
echo "<div style='text-align:center;'>
        <img src='" . htmlspecialchars($picha) . "' alt='Picha ya " . htmlspecialchars($row['jina']) . "' 
             style='width:120px; height:120px; border-radius:50%; border:3px solid #ffc107; margin-bottom:10px;'>
        <h3 style='color:#0d47a1;'>" . htmlspecialchars($row['jina']) . "</h3>
        <p style='color:#333;'><strong>Namba ya simu:</strong> " . htmlspecialchars($row['simuna']) . "</p>
        <p style='color:#333;'><strong>Mzazi:</strong> " . htmlspecialchars($mzazi) . "</p>
      </div>";
?>
