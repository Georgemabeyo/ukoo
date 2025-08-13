<?php include 'config.php'; ?>  
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo</title>
<style>
body { background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222; user-select: none; padding-bottom:50px; margin:0; }
.tree-container { max-width: 900px; margin:2rem auto 3rem; background:#fff; padding:2rem 2rem 3rem; border-radius:16px; box-shadow:0 6px 20px rgba(0,0,0,0.12); }
h2.text-center { font-size: clamp(1.8rem,4vw,2.6rem); font-weight:900; margin-bottom:2rem; color:#ffc107; text-shadow:1px 1px 2px rgba(0,0,0,0.2); text-align:center; user-select:none; }
.tree, .tree ul { list-style:none; padding-left:1.5rem; margin:0; position:relative; }
.tree ul::before { content:''; position:absolute; top:0; left:15px; bottom:0; border-left:2px solid #ffc107; }
.tree li { position:relative; padding-left:42px; margin-bottom:1rem; }
.tree li::before { content:''; position:absolute; top:1.25rem; left:0; width:38px; border-top:2px solid #ffc107; border-radius:3px; }
.member { display:flex; align-items:center; background:#0d47a1; padding:0.45rem 1rem; border-radius:12px; box-shadow:0 0 8px rgba(255,193,7,0.6); cursor:pointer; transition: background-color 0.3s ease, box-shadow 0.3s ease; position:relative; user-select:text; }
.member:hover, .member:focus { background-color:#ffc107; color:#0d47a1; box-shadow:0 0 14px rgba(255,193,7,0.9); outline:none; }
.member img { width:54px; height:54px; object-fit:cover; border-radius:50%; margin-right:1rem; border:3px solid #ffc107; box-shadow:0 0 6px rgba(255,193,7,0.8); flex-shrink:0; }
.member p { margin:0; font-weight:700; font-size:clamp(0.95rem,1.5vw,1.15rem); color:#ffc107; text-shadow:1px 1px 2px rgba(0,0,0,0.15); flex-grow:1; }
.view-children-btn { flex-shrink:0; font-size:1.35rem; color:#ffc107; background:#0d47a1; border-radius:50%; width:30px; height:30px; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 0 6px rgba(255,193,7,0.6); transition: background-color 0.3s ease, transform 0.25s ease; user-select:none; margin-left:8px; }
.view-children-btn:hover, .view-children-btn:focus { background:#ffc107; color:#0d47a1; outline:none; transform:scale(1.15); box-shadow:0 0 14px rgba(255,193,7,0.8); }
.children-list { margin-top:0.5rem; margin-left:70px; display:none; animation:slideDown 0.3s ease forwards; }
@keyframes slideDown { from {opacity:0; max-height:0;} to {opacity:1; max-height:1000px;} }
.btn-container { margin-top:2rem; text-align:center; padding:15px 40px; }
.btn-custom { display:inline-block; font-weight:700; font-size:1.15rem; padding:0.65rem 2rem; border-radius:8px; cursor:pointer; user-select:none; border:none; transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease; margin:0 10px; text-decoration:none; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.btn-ongeza { background-color:#0d47a1; color:#ffc107; box-shadow:0 5px 15px rgba(255,193,7,0.6); }
.btn-ongeza:hover, .btn-ongeza:focus { background-color:#08306b; color:#fff176; box-shadow:0 7px 22px rgba(8,48,107,0.8); outline:none; }
.btn-rudi { background-color:#ffc107; color:#0d47a1; box-shadow:0 5px 15px rgba(13,71,161,0.6); }
.btn-rudi:hover, .btn-rudi:focus { background-color:#e6b007; color:#0a356b; box-shadow:0 7px 22px rgba(230,176,7,0.8); outline:none; }
@media(max-width:600px){.tree-container{padding:1.2rem 1rem 2rem;margin:1rem auto 2rem;}.member img{width:42px;height:42px;margin-right:0.7rem;}.member p{font-size:clamp(0.85rem,3vw,1rem);}.view-children-btn{width:26px;height:26px;font-size:1.1rem;margin-left:6px;}.tree li{padding-left:34px;margin-bottom:0.7rem;}.tree li::before{top:1rem;width:28px;}.tree ul::before{left:12px;}.btn-custom{font-size:1rem;padding:0.5rem 1.5rem;margin:0 6px;}}
</style>
</head>
<body>
<div class="container tree-container">
<h2 class="text-center">Mti wa Ukoo wa Makomelelo</h2>
<div id="tree-container" class="tree" tabindex="0" aria-live="polite" aria-relevant="additions removals">

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
            echo "<div class='member' tabindex='0' role='button' data-id='$id' aria-label='Onyesha taarifa za $fullName'>";
            echo "<img src='$photo' alt='Picha ya $fullName'>";
            echo "<p>$fullName</p>";
            echo "<button class='view-children-btn' aria-label='Ona watoto wa $fullName' data-parent='$id' title='Ona watoto'><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 16 16' width='16' height='16'><path d='M1.5 3a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V4H2v8h3a.5.5 0 0 1 0 1H2a1 1 0 0 1-1-1V3z'/><path d='M15 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z'/></svg></button>";
            echo "</div>";
            echo "<div class='children-list' id='children-$id' aria-live='polite' aria-atomic='true'></div>";
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

<!-- Modal for member details -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Taarifa za Mtu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="memberDetails"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle children on clicking member
    $(document).on('click keypress', '.member', function(e){
        if(e.target.classList.contains('view-children-btn')) return; // skip icon click
        if(e.type==='click'||(e.type==='keypress'&&(e.key==='Enter'||e.key===' '))) {
            e.preventDefault();
            const id = $(this).data('id');
            const container = $('#children-' + id);
            if(container.is(':visible')) container.slideUp(250);
            else {
                if(container.children().length===0){
                    $.get('load_children.php', {parent_id:id}, function(data){
                        container.html(data);
                        container.slideDown(250);
                    });
                } else container.slideDown(250);
            }
        }
    });

    // View icon click -> load member info modal
    $(document).on('click keypress', '.view-children-btn', function(e){
        if(e.type==='click'||(e.type==='keypress'&&(e.key==='Enter'||e.key===' '))) {
            e.preventDefault();
            const parentId = $(this).data('parent');
            $.get('view_member.php', {id:parentId}, function(data){
                $('#memberDetails').html(data);
                const modal = new bootstrap.Modal(document.getElementById('memberModal'));
                modal.show();
            });
        }
    });
});
</script>
</body>
</html>
