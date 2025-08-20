<?php
include 'config.php';
session_start();

function displayTree($conn, $parent_id = null) {
    if (!$conn) {
        echo "<p style='color:red;'>Database connection failed!</p>";
        return;
    }
    if (is_null($parent_id)) {
        // Order by 'id' ascending (e.g., 11,12,13,...)
        $sql = "SELECT * FROM family_tree WHERE parent_id IS NULL ORDER BY id ASC";
        $params = [];
    } else {
        $sql = "SELECT * FROM family_tree WHERE parent_id = $1 ORDER BY id ASC";
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

<!-- Modal na overlay -->
<div id="modal-overlay" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;"></div>
<div id="member-modal" style="display:none;position:fixed;top:20%;left:50%;transform:translateX(-50%);padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.5);max-width:400px;z-index:1000;">
    <button id="close-modal">X</button>
    <div id="modal-content"></div>
</div>

<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="scripts.js"></script>
<script>
$(document).on('click keypress', '.member', function(e) {
    if(e.type === 'keypress' && ![13,32].includes(e.which)) return;
    if($(e.target).hasClass('view-children-btn')) return;
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

$(document).on('click', '.view-children-btn', function(e){
    e.stopPropagation();
    const parentId = $(this).data('parent');
    $.get('view_member.php', {id: parentId}, function(data){
        $('#modal-content').html(data);
        $('#modal-overlay, #member-modal').fadeIn(200);
        // Adjust modal colors based on dark mode
        if(document.body.classList.contains('dark-mode')){
            $('#member-modal').css({'background-color':'#222', 'color':'#eee', 'box-shadow':'0 0 15px rgba(0,0,0,0.9)'});
            $('#close-modal').css('color', '#eee');
        } else {
            $('#member-modal').css({'background-color':'#fff', 'color':'#000', 'box-shadow':'0 0 10px rgba(0,0,0,0.5)'});
            $('#close-modal').css('color', '#000');
        }
    });
});

$('#close-modal, #modal-overlay').on('click', function(){
    $('#modal-content').html('');
    $('#modal-overlay, #member-modal').fadeOut(200);
});
</script>
</body>
</html>
