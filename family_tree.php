<?php

include 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);

function displayTree($conn, $parent_id = null) {
    if (!$conn) {
        echo "<p style='color:red;'>Database connection failed!</p>";
        return;
    }
    if (is_null($parent_id)) {
        $sql = "SELECT * FROM family_tree WHERE parent_id IS NULL ORDER BY first_name,last_name";
        $params = [];
    } else {
        $sql = "SELECT * FROM family_tree WHERE parent_id=$1 ORDER BY first_name,last_name";
        $params = [$parent_id];
    }
    $result = pg_query_params($conn, $sql, $params);
    if ($result && pg_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            $id = (int)$row['id'];
            $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
            $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/60?text=No+Image";
            echo "<li>";
            echo "<div class='member' data-id='$id' tabindex='0' role='button' aria-expanded='false' aria-controls='children-$id'>";
            echo "<img src='$photo' alt='Picha ya $fullName'>";
            echo "<p class='member-name'>$fullName</p>";
            echo "<button class='view-children-btn' tabindex='-1' data-parent='$id' title='Ona taarifa'>i</button>";
            echo "</div>";
            echo "<div class='children-list' id='children-$id' aria-hidden='true'></div>";
            echo "</li>";
        }
        echo "</ul>";
    } elseif ($result === false) {
        echo "<p style='color:red;'>Tatizo la ku-query database: " . pg_last_error($conn) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Mti wa Ukoo | Ukoo wa Makomelelo</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<?php include 'header.php'; ?>

<div class="container tree-container">
    <h2 class="text-center">Mti wa Ukoo wa Makomelelo</h2>
    <div id="tree-container" class="tree">
        <?php displayTree($conn, null); ?>
    </div>
    <div class="btn-container">
        <a href="registration.php" class="btn-custom">Ongeza Mtu Mpya</a>
        <a href="index.php" class="btn-custom">Rudi Nyumbani</a>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="scripts.js"></script>
<script>
// Keyboard accessibility and children toggle
$(document).on('click keypress', '.member', function(e) {
    if(e.type === 'keypress' && ![13,32].includes(e.which)) return; // Only Enter or Space keys
    if($(e.target).hasClass('view-children-btn')) return; // Skip if info button clicked
    const id = $(this).data('id');
    const container = $('#children-' + id);
    if(container.is(':visible')){
        container.slideUp(200);
        $(this).attr('aria-expanded', 'false');
        container.attr('aria-hidden', 'true');
    } else {
        if(container.children().length === 0){
            $.get('load_children.php', {parent_id: id}, function(data){
                container.html(data).slideDown(200);
                $(this).attr('aria-expanded', 'true');
                container.attr('aria-hidden', 'false');
            }.bind(this));
        } else {
            container.slideDown(200);
            $(this).attr('aria-expanded', 'true');
            container.attr('aria-hidden', 'false');
        }
    }
});

// View member info
$(document).on('click', '.view-children-btn', function(e){
    e.stopPropagation();
    const parentId = $(this).data('parent');
    $.get('view_member.php', {id: parentId}, function(data){
        alert(data); // Replace with modal or nicer display
    });
});
</script>
</body>
</html>
