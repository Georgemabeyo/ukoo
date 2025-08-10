<?php include 'config.php'; ?> 
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%);
        }

        .tree-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .tree {
            list-style: none;
            padding-left: 1rem;
            position: relative;
        }

        .tree ul {
            padding-left: 1.5rem;
            list-style: none;
            position: relative;
        }

        .tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 12px;
            bottom: 0;
            border-left: 2px solid #0d6efd; /* Bootstrap primary blue */
        }

        .tree li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 1rem;
        }

        .tree li::before {
            content: '';
            position: absolute;
            top: 1.5rem;
            left: 0;
            width: 25px;
            border-top: 2px solid #0d6efd;
        }

        .member {
            display: flex;
            align-items: center;
            background: #e7f1ff; /* light blue box */
            padding: 0.7rem 1rem;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(13,110,253,0.15);
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .member:hover {
            background-color: #cfe3ff;
            box-shadow: 0 0 12px rgba(13,110,253,0.3);
        }
        .member img {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 1rem;
            border: 3px solid #0d6efd;
            flex-shrink: 0;
            box-shadow: 0 0 5px rgba(13,110,253,0.4);
        }
        .member p {
            margin: 0;
            font-weight: 700;
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: #ff8800; /* Orange text */
            user-select: none;
            word-break: break-word;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.15);
        }

        h2.text-center {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 900;
            margin-bottom: 2.5rem;
            color: #0d6efd;
            user-select: none;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        /* Buttons */
        .btn {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-success {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 12px rgba(13,110,253,0.6);
        }
        .btn-success:hover {
            background-color: #084bcc;
            border-color: #084bcc;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            box-shadow: 0 4px 10px rgba(108,117,125,0.6);
        }
        .btn-secondary:hover {
            background-color: #565e64;
            border-color: #565e64;
        }

        /* Responsive - small screens */
        @media (max-width: 600px) {
            .tree-container {
                padding: 1rem 1rem;
            }
            .member img {
                width: 50px;
                height: 50px;
                margin-right: 0.75rem;
            }
            .member p {
                font-size: clamp(1rem, 3vw, 1.15rem);
            }
            .tree li::before {
                top: 1.3rem;
                width: 20px;
            }
            .tree li {
                padding-left: 24px;
                margin-bottom: 0.75rem;
            }
            h2.text-center {
                font-size: clamp(1.5rem, 5vw, 2rem);
            }
        }

        /* Modal tweaks */
        #memberModal .modal-content {
            border-radius: 12px;
            padding: 1rem;
            background: #f0f7ff;
            box-shadow: 0 6px 18px rgba(13,110,253,0.25);
        }
        #memberModal .modal-title {
            font-size: clamp(1.3rem, 2.5vw, 1.7rem);
            font-weight: 900;
            color: #0d6efd;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        #memberDetails {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: #222;
            word-wrap: break-word;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container py-5 tree-container" role="main" aria-label="Mti wa familia wa Ukoo wa Makomelelo">
    <h2 class="text-center">Mti wa Ukoo wa Makomelelo</h2>
    <div id="tree-container" class="tree" tabindex="0">
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
                    echo "<li>";
                    $photo = !empty($row['photo']) ? "uploads/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/80?text=No+Image";
                    echo "<div class='member' data-id='" . htmlspecialchars($row['id']) . "' tabindex='0' role='button' aria-pressed='false'>
                            <img src='" . $photo . "' alt='Picha ya " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "'>
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
        <a href="register.php" class="btn btn-success me-2" role="button">Ongeza Mtu Mpya</a>
        <a href="index.php" class="btn btn-secondary" role="button">Rudi Nyumbani</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true" aria-labelledby="memberModalTitle" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="memberModalTitle">Taarifa za Mtu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Funga"></button>
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
$(document).on('click keypress', '.member', function(e){
    // Allow click or keyboard Enter/Space keys
    if(e.type === 'click' || (e.type === 'keypress' && (e.key === 'Enter' || e.key === ' '))) {
        e.preventDefault();
        var id = $(this).data('id');
        var pressed = $(this).attr('aria-pressed') === 'true';
        $(this).attr('aria-pressed', !pressed);
        $.get('view_member.php', {id: id}, function(data){
            $('#memberDetails').html(data);
            var modal = new bootstrap.Modal(document.getElementById('memberModal'));
            modal.show();
        });
    }
});
</script>
</body>
</html>
