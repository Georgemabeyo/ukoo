<?php include 'config.php'; ?>  
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo</title>
<style>
body {
    background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #222;
    user-select: none;
    padding-bottom: 50px;
    margin: 0;
}
.tree-container {
    max-width: 900px;
    margin: 2rem auto 3rem;
    background: #fff;
    padding: 2rem 2rem 3rem;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
h2.text-center {
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 900;
    margin-bottom: 2rem;
    color: #ffc107;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    text-align: center;
}
.tree, .tree ul {
    list-style: none;
    padding-left: 1.5rem;
    margin: 0;
    position: relative;
}
.tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 15px;
    bottom: 0;
    border-left: 2px solid #ffc107;
}
.tree li {
    position: relative;
    padding-left: 42px;
    margin-bottom: 1rem;
}
.tree li::before {
    content: '';
    position: absolute;
    top: 1.25rem;
    left: 0;
    width: 38px;
    border-top: 2px solid #ffc107;
    border-radius: 3px;
}
/* Member block */
.member {
    display: flex;
    align-items: center;
    background: #0d47a1;
    padding: 0.45rem 1rem;
    border-radius: 12px;
    box-shadow: 0 0 8px rgba(255,193,7,0.6);
    cursor: pointer;
    transition: box-shadow 0.3s ease;
    position: relative;
    user-select: text;
}
.member:hover {
    box-shadow: 0 0 14px rgba(255,193,7,0.9);
}
.member img {
    width: 54px;
    height: 54px;
    object-fit: cover;
    border-radius: 50%;
    margin-right: 1rem;
    border: 3px solid #ffc107;
    box-shadow: 0 0 6px rgba(255,193,7,0.8);
}
.member p {
    margin: 0;
    font-weight: 700;
    font-size: clamp(0.95rem, 1.5vw, 1.15rem);
    color: #ffc107;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.15);
    flex-grow: 1;
}
.view-children-btn {
    flex-shrink: 0;
    font-size: 1.35rem;
    color: #ffc107;
    background: #0d47a1;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 0 6px rgba(255,193,7,0.6);
    margin-left: 8px;
    transition: transform 0.25s ease, box-shadow 0.3s ease;
}
.view-children-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 0 14px rgba(255,193,7,0.8);
}
.children-list {
    margin-top: 0.5rem;
    margin-left: 70px;
    display: none;
}
</style>
</head>
<body>
<div class="container tree-container">
<h2 class="text-center">Mti wa Ukoo wa Makomelelo</h2>
<div id="tree-container" class="tree">
<?php
function displayTree($parent_id = null, $conn) {
    if (is_null($parent_id)) {
        $sql = "SELECT * FROM family_tree WHERE parent_id IS NULL ORDER BY first_name, last_name";
    } else {
        $parent_id = (int)$parent_id;
        $sql = "SELECT * FROM family_tree WHERE parent_id = $parent_id ORDER BY first_name, last_name";
    }
    $result = pg_query($conn, $sql);
    if ($result && pg_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            $id = (int)$row['id'];
            $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
            $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/80?text=No+Image";
            echo "<li>";
            echo "<div class='member' data-id='$id'>";
            echo "<img src='$photo' alt='Picha ya $fullName'>";
            echo "<p class='member-name'>$fullName</p>";
            echo "<button class='view-children-btn' data-parent='$id' title='Ona watoto'>i</button>";
            echo "</div>";
            echo "<div class='children-list' id='children-$id'></div>";
            echo "</li>";
        }
        echo "</ul>";
    }
}
displayTree(null, $conn);
?>
</div>
<div class="btn-container">
    <a href="registration.php" class="btn-custom btn-ongeza">Ongeza Mtu Mpya</a>
    <a href="index.php" class="btn-custom btn-rudi">Rudi Nyumbani</a>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle children list on clicking member block
    $(document).on('click', '.member', function(e){
        if($(e.target).hasClass('view-children-btn')) return;
        const id = $(this).data('id');
        const container = $('#children-' + id);
        if(container.is(':visible')) {
            container.slideUp(250);
        } else {
            if(container.children().length === 0) {
                $.get('load_children.php', {parent_id: id}, function(data){
                    container.html(data);
                    container.slideDown(250);
                });
            } else {
                container.slideDown(250);
            }
        }
    });

    // View info button - show modal
    $(document).on('click', '.view-children-btn', function(e){
        e.stopPropagation(); // prevent opening children
        const parentId = $(this).data('parent');
        $.get('view_member.php', {id: parentId}, function(data){
            $('#memberDetails').html(data);
            const modal = new bootstrap.Modal(document.getElementById('memberModal'));
            modal.show();
        });
    });
});
</script>
</body>
</html>
