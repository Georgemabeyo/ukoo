<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Mti wa Ukoo wa Makomelelo</h2>
    <div id="tree-container" class="tree">
        <?php
        function displayTree($parent_id = null, $conn) {
            $sql = is_null($parent_id) ? 
                "SELECT * FROM family_tree WHERE parent_id IS NULL" :
                "SELECT * FROM family_tree WHERE parent_id = $parent_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>";
                    $photo = $row['photo'] ? "uploads/".$row['photo'] : "https://via.placeholder.com/80";
                    echo "<div class='member' data-id='{$row['id']}'>
                            <img src='{$photo}' alt='Picha'>
                            <p>{$row['first_name']} {$row['last_name']}</p>
                          </div>";
                    displayTree($row['id'], $conn);
                    echo "</li>";
                }
                echo "</ul>";
            }
        }
        displayTree(null, $conn);
        ?>
    </div>
    <div class="text-center mt-4">
        <a href="register.php" class="btn btn-success">Ongeza Mtu Mpya</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Taarifa za Mtu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="memberDetails">
        <p>Inapakia...</p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).on('click', '.member', function(){
    var id = $(this).data('id');
    $.get('view_member.php', {id: id}, function(data){
        $('#memberDetails').html(data);
        var modal = new bootstrap.Modal(document.getElementById('memberModal'));
        modal.show();
    });
});
</script>
</body>
</html>
