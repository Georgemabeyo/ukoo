<?php include 'config.php'; ?>  
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo</title>
<style>
body { background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding-bottom:80px; color:#222; }
.tree-container { max-width:960px; margin:2rem auto; background:#fff; padding:1.5rem; border-radius:16px; box-shadow:0 6px 20px rgba(0,0,0,0.15); }
h2.text-center { font-size:clamp(1.6rem, 4vw, 2.4rem); font-weight:900; margin-bottom:1.5rem; color:#ffc107; text-align:center; text-shadow:1px 1px 2px rgba(0,0,0,0.2); }
.tree, .tree ul { list-style:none; padding-left:1rem; margin:0; position:relative; }
.tree ul::before { content:''; position:absolute; top:0; left:20px; bottom:0; border-left:2px solid #ffc107aa; }
.tree li { position:relative; padding-left:40px; margin-bottom:1rem; }
.tree li::before { content:''; position:absolute; top:1.2rem; left:0; width:35px; border-top:2px solid #ffc107aa; border-radius:3px; }
.member { display:flex; align-items:center; background:#0d47a1; padding:0.4rem 0.8rem; border-radius:10px; box-shadow:0 3px 8px rgba(13,71,161,0.4); cursor:pointer; transition:transform 0.2s, box-shadow 0.3s; }
.member:hover { transform:translateY(-2px); box-shadow:0 5px 12px rgba(255,193,7,0.7); }
.member img { width:48px; height:48px; object-fit:cover; border-radius:50%; margin-right:0.6rem; border:2px solid #ffc107; flex-shrink:0; }
.member p { margin:0; font-weight:600; font-size:clamp(0.9rem,1.5vw,1.1rem); color:#ffc107; flex-grow:1; }
.view-children-btn { flex-shrink:0; font-size:1.1rem; color:#ffc107; background:#0d47a1; border-radius:50%; width:28px; height:28px; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 0 5px rgba(255,193,7,0.6); margin-left:6px; transition:transform 0.25s, box-shadow 0.3s; }
.view-children-btn:hover { transform:scale(1.15); box-shadow:0 0 10px rgba(255,193,7,0.9); }
.children-list { margin-top:0.4rem; padding-left:1.5rem; display:none; }
.btn-container { display:flex; justify-content:center; gap:0.8rem; margin-top:1.5rem; flex-wrap:wrap; }
.btn-custom { padding:0.6rem 1.5rem; font-weight:700; font-size:0.95rem; border-radius:8px; text-decoration:none; color:#ffeb3b; background:#0d47a1; box-shadow:0 3px 8px rgba(13,71,161,0.4); transition:transform 0.2s, box-shadow 0.3s, background 0.3s; }
.btn-custom:hover { transform:translateY(-2px); box-shadow:0 5px 12px rgba(13,71,161,0.6); background:#074078; }
@media(max-width:768px){ .tree li::before{width:28px;} .tree ul::before{left:15px;} .tree li{padding-left:32px;} .member img{width:42px;height:42px;} }
@media(max-width:480px){ .tree li{padding-left:28px;} .tree ul::before{left:12px;} .member img{width:38px;height:38px;} .member p{font-size:clamp(0.85rem,2.5vw,1rem);} .view-children-btn{width:24px;height:24px;font-size:0.9rem;} }
</style>
</head>
<body>
<div class="container tree-container">
<h2 class="text-center">Mti wa Ukoo wa Makomelelo</h2>
<div id="tree-container" class="tree">
<?php
function displayTree($parent_id = null, $conn) {
    if (!$conn) {
        echo "<p style='color:red;'>Database connection failed!</p>";
        return;
    }
    if (is_null($parent_id)) {
        $sql = "SELECT * FROM family_tree WHERE parent_id IS NULL ORDER BY first_name, last_name";
    } else {
        $parent_id = (int)$parent_id;
        $sql = "SELECT * FROM family_tree WHERE parent_id = $parent_id ORDER BY first_name, last_name";
    }

    $result = @pg_query($conn, $sql); // suppress warning
    if ($result && pg_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            $id = (int)$row['id'];
            $first_name = $row['first_name'] ?? '';
            $last_name = $row['last_name'] ?? '';
            $fullName = htmlspecialchars($first_name . " " . $last_name);
            $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/60?text=No+Image";
            echo "<li>";
            echo "<div class='member' data-id='$id'>";
            echo "<img src='$photo' alt='Picha ya $fullName'>";
            echo "<p class='member-name'>$fullName</p>";
            echo "<button class='view-children-btn' data-parent='$id' title='Ona taarifa'>i</button>";
            echo "</div>";
            echo "<div class='children-list' id='children-$id'></div>";
            echo "</li>";
        }
        echo "</ul>";
    } elseif ($result === false) {
        echo "<p style='color:red;'>Tatizo la ku-query database: " . pg_last_error($conn) . "</p>";
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
$(document).ready(function(){
    $(document).on('click', '.member', function(e){
        if($(e.target).hasClass('view-children-btn')) return;
        const id = $(this).data('id');
        const container = $('#children-' + id);
        if(container.is(':visible')){
            container.slideUp(200);
        } else {
            if(container.children().length === 0){
                $.get('load_children.php', {parent_id:id}, function(data){
                    container.html(data);
                    container.slideDown(200);
                });
            } else {
                container.slideDown(200);
            }
        }
    });

    $(document).on('click', '.view-children-btn', function(e){
        e.stopPropagation();
        const parentId = $(this).data('parent');
        $.get('view_member.php', {id:parentId}, function(data){
            alert(data); // unaweza badilisha na modal popup ikiwa unataka
        });
    });
});
</script>
</body>
</html>
