<?php
include 'config.php';

if (!isset($_GET['parent_id'])) {
    exit("Parent ID required");
}

$parent_id = (int)$_GET['parent_id'];

// Fetch children of the parent
$sql = "SELECT * FROM family_tree WHERE parent_id = $parent_id ORDER BY first_name, last_name";
$result = pg_query($conn, $sql);

if ($result && pg_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = pg_fetch_assoc($result)) {
        $id = (int)$row['id'];
        $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
        $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/80?text=No+Image";

        echo "<li>";
        echo "<div class='member' tabindex='0' role='button' aria-pressed='false' data-id='$id' aria-label='Onyesha taarifa za $fullName'>";
        echo "<img src='$photo' alt='Picha ya $fullName'>";
        echo "<p>$fullName</p>";
        echo "<button class='view-children-btn' aria-label='Ona watoto wa $fullName' data-parent='$id' title='Ona watoto'><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 16 16' width='16' height='16'><path d='M1.5 3a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V4H2v8h3a.5.5 0 0 1 0 1H2a1 1 0 0 1-1-1V3z'/><path d='M15 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z'/></svg></button>";
        echo "</div>";

        // Recursive container for grandchildren
        echo "<div class='children-list' id='children-$id' aria-live='polite' aria-atomic='true'></div>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='margin-left: 1rem; color:#555;'>Hakuna watoto waliopo.</p>";
}
?>
