<?php
include 'config.php';

if (!isset($_GET['id'])) {
    exit("Member ID required");
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM family_tree WHERE id = $id LIMIT 1";
$result = pg_query($conn, $sql);

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
    $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/150?text=No+Image";
    $phone = !empty($row['phone']) ? htmlspecialchars($row['phone']) : 'Hakuna namba';
    $parent_id = $row['parent_id'];

    // Optional: fetch parent name
    if ($parent_id) {
        $pRes = pg_query($conn, "SELECT first_name, last_name FROM family_tree WHERE id = $parent_id LIMIT 1");
        if ($pRes && pg_num_rows($pRes) > 0) {
            $pRow = pg_fetch_assoc($pRes);
            $parentName = htmlspecialchars($pRow['first_name'] . " " . $pRow['last_name']);
        } else {
            $parentName = "Hayupo";
        }
    } else {
        $parentName = "Hakuna";
    }

    echo "<div style='text-align:center;'>";
    echo "<img src='$photo' alt='Picha ya $fullName' style='width:120px; height:120px; border-radius:50%; border:3px solid #ffc107; margin-bottom:10px;'>";
    echo "<h3 style='color:#0d47a1;'>$fullName</h3>";
    echo "<p style='color:#333;'><strong>Namba ya simu:</strong> $phone</p>";
    echo "<p style='color:#333;'><strong>Mzazi:</strong> $parentName</p>";
    echo "</div>";
} else {
    echo "<p>Mwanaukoo hayupo.</p>";
}
?>
