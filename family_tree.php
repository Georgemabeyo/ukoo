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
    margin: 0;
    padding-bottom: 80px;
    color: #222;
}
.tree-container {
    max-width: 960px;
    margin: 2rem auto;
    background: #fff;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
h2.text-center {
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 900;
    margin-bottom: 2rem;
    color: #ffc107;
    text-align: center;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}
.tree, .tree ul {
    list-style: none;
    padding-left: 1rem;
    margin: 0;
    position: relative;
}
.tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 25px;
    bottom: 0;
    border-left: 2px solid #ffc107aa;
}
.tree li {
    position: relative;
    padding-left: 50px;
    margin-bottom: 1.5rem;
}
.tree li::before {
    content: '';
    position: absolute;
    top: 1.5rem;
    left: 0;
    width: 45px;
    border-top: 2px solid #ffc107aa;
    border-radius: 3px;
}
/* Member block */
.member {
    display: flex;
    align-items: center;
    background: #0d47a1;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(13,71,161,0.4);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.3s;
    word-wrap: break-word;
}
.member:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255,193,7,0.7);
}
.member img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
    margin-right: 1rem;
    border: 3px solid #ffc107;
    flex-shrink: 0;
}
.member p {
    margin: 0;
    font-weight: 700;
    font-size: clamp(0.95rem, 1.5vw, 1.2rem);
    color: #ffc107;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    flex-grow: 1;
}
.view-children-btn {
    flex-shrink: 0;
    font-size: 1.3rem;
    color: #ffc107;
    background: #0d47a1;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 0 6px rgba(255,193,7,0.6);
    margin-left: 8px;
    transition: transform 0.25s, box-shadow 0.3s;
}
.view-children-btn:hover {
    transform: scale(1.2);
    box-shadow: 0 0 14px rgba(255,193,7,0.9);
}
.children-list {
    margin-top: 0.5rem;
    margin-left: 80px;
    display: none;
}

/* Buttons below tree */
.btn-container {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}
.btn-custom {
    padding: 0.8rem 2rem;
    font-weight: 700;
    font-size: 1rem;
    border-radius: 12px;
    text-decoration: none;
    color: #ffeb3b;
    background: #0d47a1;
    box-shadow: 0 4px 12px rgba(13,71,161,0.4);
    transition: transform 0.2s, box-shadow 0.3s, background 0.3s;
}
.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(13,71,161,0.6);
    background: #074078;
}

/* Responsive tweaks */
@media(max-width:768px){
    .tree li::before {width: 35px;}
    .tree ul::before {left:20px;}
    .tree li {padding-left:40px;}
    .member img {width:50px; height:50px;}
}
@media(max-width:480px){
    .tree li {padding-left:35px;}
    .tree ul::before {left:15px;}
    .member img {width:45px; height:45px;}
    .member p {font-size: clamp(0.85rem, 2.5vw, 1rem);}
    .view-children-btn {width:28px; height:28px; font-size:1rem;}
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
    <a href="registration.php" class="btn-custom">Ongeza Mtu Mpya</a>
    <a href="index.php" class="btn-custom">Rudi Nyumbani</a>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
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

    $(document).on('click', '.view-children-btn', function(e){
        e.stopPropagation();
        const parentId = $(this).data('parent');
        $.get('view_member.php', {id: parentId}, function(data){
            alert(data); // or modal logic
        });
    });
});
</script>
</body>
</html>
