<?php
include 'config.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Mti wa Ukoo | Ukoo wa Makomelelo</title>
<style>
body, html {
    margin:0; padding:0;
    font-family:'Segoe UI', sans-serif;
    transition: background 0.3s, color 0.3s;
}
body.light-mode {
    background:#f0f4f8;
    color:#222;
}
body.dark-mode {
    background:#1e293b;
    color:#f8fafc;
}

/* Header/Navbar */
header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 25px;
    border-radius:0 0 15px 15px;
    background:linear-gradient(90deg,#0d47a1,#1976d2);
    position:relative; z-index:1000;
}
.logo {
    font-size:1.8rem;
    font-weight:700;
    color:#ffc107;
}
.nav-links {
    display:flex;
    gap:15px;
    align-items:center;
    flex-wrap:wrap;
}
.nav-links a {
    color:#ffc107;
    font-weight:600;
    padding:8px 12px;
    border-radius:6px;
    transition:0.3s;
    text-decoration:none; /* no underline */
}
.nav-links a:hover,
.nav-links a.active {
    background:#ffc107;
    color:#0d47a1;
}

/* Toggle button for mobile */
.nav-toggle {
    display:none;
    flex-direction:column;
    justify-content:space-between;
    width:30px;
    height:24px;
    background:transparent;
    border:none;
    cursor:pointer;
    z-index:1100;
}
.nav-toggle span {
    display:block;
    height:3px;
    background:#ffc107;
    border-radius:2px;
    transition:all 0.4s;
}
.nav-toggle.active span:nth-child(1){
    transform:rotate(45deg) translate(5px,5px);
}
.nav-toggle.active span:nth-child(2){
    opacity:0;
}
.nav-toggle.active span:nth-child(3){
    transform:rotate(-45deg) translate(5px,-5px);
}

/* Tree Container */
.tree-container {
    max-width: 850px;
    margin: 40px auto;
    background:#fff;
    padding:1.5rem;
    border-radius:16px;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    transition: background 0.3s,color 0.3s;
}
body.dark-mode .tree-container {
    background:#334155;
    color:#f8fafc;
}

/* Title */
h2.text-center {
    font-size:clamp(1.6rem,4vw,2.4rem);
    font-weight:900;
    margin-bottom:1.5rem;
    color:#ffc107;
    text-align:center;
    text-shadow:1px 1px 2px rgba(0,0,0,0.2);
}

/* Tree Styles */
.tree, .tree ul {
    list-style:none;
    padding-left:1rem;
    margin:0;
    position:relative;
}
.tree ul::before {
    content:'';
    position:absolute;
    top:0;
    left:20px;
    bottom:0;
    border-left:2px solid #ffc107cc;
}
.tree li {
    position:relative;
    padding-left:34px;
    margin-bottom:0.9rem;
}
.tree li::before {
    content:'';
    position:absolute;
    top:1.15rem;
    left:0;
    width:30px;
    border-top:2px solid #ffc107cc;
    border-radius:3px;
}

/* Member box */
.member {
    display:flex;
    align-items:center;
    background:#0d47a1;
    padding:0.35rem 0.75rem;
    border-radius:10px;
    box-shadow:0 3px 8px rgba(13,71,161,0.4);
    cursor:pointer;
    transition:transform 0.2s, box-shadow 0.3s;
    min-width: 200px;
}
.member:hover {
    transform:translateY(-2px);
    box-shadow:0 5px 12px rgba(255,193,7,0.7);
}
.member img {
    width:44px;
    height:44px;
    object-fit:cover;
    border-radius:50%;
    margin-right:0.5rem;
    border:2px solid #ffc107;
    flex-shrink:0;
}
.member p {
    margin:0;
    font-weight:600;
    font-size:clamp(0.85rem,1.3vw,1rem);
    color:#ffc107;
    flex-grow:1;
}

/* View children button (info icon) */
.view-children-btn {
    flex-shrink:0;
    font-size:1.1rem;
    color:#ffc107;
    background:#0d47a1;
    border-radius:50%;
    width:28px;
    height:28px;
    border:none;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 0 6px rgba(255,193,7,0.7);
    margin-left:6px;
    transition:transform 0.25s, box-shadow 0.3s;
    font-family: monospace;
    font-weight: 900;
    line-height: 1;
}
.view-children-btn:hover {
    transform:scale(1.15);
    box-shadow:0 0 12px rgba(255,193,7,0.9);
}
.children-list {
    margin-top:0.4rem;
    padding-left:1.5rem;
    display:none;
}

/* Buttons container */
.btn-container {
    display:flex;
    justify-content:center;
    gap:0.8rem;
    margin-top:1.5rem;
    flex-wrap:wrap;
}
.btn-custom {
    padding:0.55rem 1.35rem;
    font-weight:700;
    font-size:0.95rem;
    border-radius:8px;
    text-decoration:none;
    color:#ffeb3b;
    background:#0d47a1;
    box-shadow:0 3px 8px rgba(13,71,161,0.4);
    transition:transform 0.2s, box-shadow 0.3s, background 0.3s;
}
.btn-custom:hover {
    transform:translateY(-2px);
    box-shadow:0 5px 12px rgba(13,71,161,0.6);
    background:#074078;
}

/* Footer */
footer {
    text-align:center;
    padding:20px;
    border-radius:15px;
    background:#2563eb;
    color:#facc15;
    margin-top:50px;
}

/* Responsive */
@media(max-width:768px) {
    .nav-links {
        flex-direction:column;
        position:absolute;
        top:100%;
        right:20px;
        background:linear-gradient(180deg,#0d47a1,#1976d2);
        border-radius:10px;
        overflow:hidden;
        max-height:0;
        transition:max-height 0.35s ease, box-shadow 0.35s;
        z-index: 1000;
    }
    .nav-links.show {
        max-height:500px;
        box-shadow:0 8px 16px rgba(0,0,0,0.3);
    }
    .nav-toggle {
        display:flex;
    }
    .tree li::before {
        width:26px;
    }
    .tree ul::before {
        left:15px;
    }
    .tree li {
        padding-left:28px;
    }
    .member img {
        width:38px;
        height:38px;
    }
}
@media(max-width:480px) {
    .tree li {
        padding-left:24px;
    }
    .tree ul::before {
        left:12px;
    }
    .member img {
        width:34px;
        height:34px;
    }
    .member p {
        font-size:clamp(0.8rem,2.5vw,0.9rem);
    }
    .view-children-btn {
        width:24px;
        height:24px;
        font-size:0.9rem;
    }
}
</style>
</head>
<body class="light-mode">

<header>
    <div class="logo">Ukoo wa Makomelelo</div>
    <button class="nav-toggle" aria-label="Toggle navigation"><span></span><span></span><span></span></button>
    <nav class="nav-links">
        <a href="index.php" class="<?= $currentPage=='index.php' ? 'active' : '' ?>">Nyumbani</a>
        <a href="registration.php" class="<?= $currentPage=='registration.php' ? 'active' : '' ?>">Jisajiri</a>
        <a href="family_tree.php" class="<?= $currentPage=='family_tree.php' ? 'active' : '' ?>">Ukoo</a>
        <a href="events.php" class="<?= $currentPage=='events.php' ? 'active' : '' ?>">Matukio</a>
        <a href="contact.php" class="<?= $currentPage=='contact.php' ? 'active' : '' ?>">Mawasiliano</a>
        <?php if ($isLoggedIn): ?>
        <a href="logout.php">Toka</a>
        <?php else: ?>
        <a href="login.php">Ingia</a>
        <?php endif; ?>
        <span id="toggleTheme" style="cursor:pointer; font-weight:700;">Dark Mode</span>
    </nav>
</header>

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
    <div class="btn-container">
        <a href="registration.php" class="btn-custom">Ongeza Mtu Mpya</a>
        <a href="index.php" class="btn-custom">Rudi Nyumbani</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Navbar toggle (hamburger) toggle nav display
const toggleBtn = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
toggleBtn.addEventListener('click', () => {
    toggleBtn.classList.toggle('active');
    navLinks.classList.toggle('show');
});

// Close nav if click outside nav-links when nav is open
document.addEventListener('click', (e) => {
    if (!navLinks.contains(e.target) && !toggleBtn.contains(e.target)) {
        if (navLinks.classList.contains('show')) {
            navLinks.classList.remove('show');
            toggleBtn.classList.remove('active');
        }
    }
});

// Theme toggle with localStorage persistence
const themeToggle = document.getElementById('toggleTheme');
const storedTheme = localStorage.getItem('theme');

function applyTheme(theme) {
    if (theme === 'dark-mode') {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
        themeToggle.textContent = 'Light Mode';
    } else {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
        themeToggle.textContent = 'Dark Mode';
    }
}
// On page load apply saved theme or default light
applyTheme(storedTheme || 'light-mode');

themeToggle.addEventListener('click', () => {
    if (document.body.classList.contains('light-mode')) {
        applyTheme('dark-mode');
        localStorage.setItem('theme', 'dark-mode');
    } else {
        applyTheme('light-mode');
        localStorage.setItem('theme', 'light-mode');
    }
});

// Tree children toggle
$(document).ready(function () {
    $(document).on('click', '.member', function (e) {
        // Ignore if clicked on the info button
        if ($(e.target).hasClass('view-children-btn')) return;
        const id = $(this).data('id');
        const container = $('#children-' + id);
        if (container.is(':visible')) {
            container.slideUp(200);
        } else {
            if (container.children().length === 0) {
                $.get('load_children.php', { parent_id: id }, function (data) {
                    container.html(data);
                    container.slideDown(200);
                });
            } else {
                container.slideDown(200);
            }
        }
    });

    $(document).on('click', '.view-children-btn', function (e) {
        e.stopPropagation();
        const parentId = $(this).data('parent');
        $.get('view_member.php', { id: parentId }, function (data) {
            alert(data); // You can replace this with a modal
        });
    });
});
</script>
</body>
</html>
