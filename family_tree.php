<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <title>Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Style ya tree container */
        .tree {
            margin: 0 auto;
            padding: 20px;
            max-width: 900px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Mti wenyewe */
        .tree ul {
            padding-left: 20px;
            list-style-type: none;
            position: relative;
        }

        .tree ul::before {
            content: '';
            border-left: 2px solid #6c757d;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 10px;
        }

        /* kila mtu */
        .tree li {
            margin: 0;
            padding: 10px 0 10px 20px;
            position: relative;
        }

        .tree li::before {
            content: '';
            border-top: 2px solid #6c757d;
            position: absolute;
            top: 25px;
            left: 0;
            width: 20px;
        }

        /* Container ya mtu mmoja */
        .member {
            display: flex;
            align-items: center;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgb(0 0 0 / 0.1);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .member:hover {
            background-color: #e9f5ff;
        }
        .member img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid #0d6efd;
        }
        .member p {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #212529;
        }

        /* Responsive - on small screens */
        @media (max-width: 600px) {
            .tree {
                padding: 10px;
                max-width: 100%;
            }
            .member img {
                width: 45px;
                height: 45px;
                margin-right: 10px;
            }
            .member p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Mti wa Ukoo wa Makomelelo</h2>
    <div id="tree-container" class="tree">
        <?php
        function displayTree($parent_id = null, $conn) {
            if (is_null($parent_id)) {
                $sql = "SELECT * FROM family_tree WHERE parent_id IS NULL ORDER BY first_name, last_name";
            } else {
                $parent_id = (int)$parent_id; // sanitize
                $sql = "SELECT * FROM family_tree WHERE parent_id = $parent_id ORDER BY first_name, last_name";
            }

            $result = pg_query($conn, $sql);

            if ($result && pg_num_rows($result) > 0) {
                echo "<ul>";
                while ($row = pg_fetch_assoc($result)) {
                    echo "<li>";
                    $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/80";
                    echo "<div class='member' data-id='" . htmlspecialchars($row['id']) . "'>
                            <img src='" . $photo . "' alt='Picha'>
                            <p>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</p>
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
        <a href="register.php" class="btn btn-success me-2">Ongeza Mtu Mpya</a>
        <a href="index.php" class="btn btn-secondary">Rudi Nyumbani</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
