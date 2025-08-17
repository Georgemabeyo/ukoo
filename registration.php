<?php
include 'config.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name      = $_POST['first_name'];
    $middle_name     = $_POST['middle_name'];
    $last_name       = $_POST['last_name'];
    $dob             = $_POST['dob'];
    $gender          = $_POST['gender'];
    $marital_status  = $_POST['marital_status'];
    $has_children    = isset($_POST['has_children']) ? 1 : 0;
    $children_male   = $_POST['children_male'] ?? 0;
    $children_female = $_POST['children_female'] ?? 0;
    $country         = $_POST['country'];
    $region          = $_POST['region'] ?? '';
    $district        = $_POST['district'] ?? '';
    $ward            = $_POST['ward'] ?? '';
    $village         = $_POST['village'] ?? '';
    $city            = $_POST['city'] ?? '';
    $phone           = $_POST['phone'];
    $email           = $_POST['email'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $parent_id       = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    // Calculate child ID
    if($parent_id){
        $res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id=$1 ORDER BY id DESC LIMIT 1", [$parent_id]);
        if($res_max && pg_num_rows($res_max)>0){
            $row_max = pg_fetch_assoc($res_max);
            $last_child_id = (int)$row_max['id'];
            $parent_digits = (string)$parent_id;
            $last_digits = substr($last_child_id, strlen($parent_digits));
            $next_digit = (int)$last_digits + 1;
            if($next_digit > 999){
                echo "<div class='alert alert-danger'>Mzazi tayari ana watoto 999</div>";
                exit;
            }
            $new_id = (int)($parent_digits . str_pad($next_digit, strlen($last_digits), '0', STR_PAD_LEFT));
        } else {
            $new_id = (int)($parent_id . '1');
        }
    } else {
        $new_id = 1;
    }

    // Photo
    $photo = '';
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo);
    }

    $sql = "INSERT INTO family_tree (
        id, first_name, middle_name, last_name, dob, gender, marital_status,
        has_children, children_male, children_female, country, region, district,
        ward, village, city, phone, email, password, photo, parent_id
    ) VALUES (
        $1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21
    )";
    $params = [
        $new_id, $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
        $has_children, $children_male, $children_female, $country, $region, $district,
        $ward, $village, $city, $phone, $email, $password, $photo, $parent_id
    ];
    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
        echo "<div class='alert alert-success text-center'>
                Usajili umefanikiwa! <a href='family_tree.php'>Angalia ukoo</a>
              </div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Kuna tatizo: " . pg_last_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usajili Ukoo - Makomelelo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
/* --- Dark/Light Mode --- */
body.light-mode { background:#f0f4f8; color:#222; transition:0.3s; }
body.dark-mode { background:#1e293b; color:#f8fafc; transition:0.3s; }

/* Header/Nav */
header { display:flex; justify-content:space-between; align-items:center; padding:15px 25px; border-radius:0 0 15px 15px; background:linear-gradient(90deg,#0d47a1,#1976d2); position:relative; z-index:1000; }
.logo { font-size:1.8rem; font-weight:700; color:#ffc107; }
.nav-links { display:flex; gap:20px; align-items:center; }
.nav-links a { color:#ffc107; font-weight:600; padding:8px 12px; border-radius:6px; transition:0.3s; }
.nav-links a:hover { background:#ffc107; color:#0d47a1; }
.nav-toggle { display:none; flex-direction:column; justify-content:space-between; width:30px; height:24px; background:transparent; border:none; cursor:pointer; }
.nav-toggle span { display:block; height:3px; background:#ffc107; border-radius:2px; transition:all 0.4s; }
.nav-toggle.active span:nth-child(1){ transform:rotate(45deg) translate(5px,5px);}
.nav-toggle.active span:nth-child(2){ opacity:0; }
.nav-toggle.active span:nth-child(3){ transform:rotate(-45deg) translate(5px,-5px); }

/* Form container */
body{font-family:'Segoe UI',sans-serif;padding:20px;min-height:100vh;display:flex;justify-content:center;align-items:flex-start;}
.container{background:#fff;padding:30px 40px;border-radius:15px;max-width:700px;width:100%;box-shadow:0 20px 40px rgba(0,0,0,0.15);}
body.dark-mode .container { background:#334155; color:#f8fafc; }
h2{text-align:center;color:#0d47a1;margin-bottom:25px;font-weight:900;}
body.dark-mode h2 { color:#facc15; }
label{font-weight:600;color:#0d47a1;}
body.dark-mode label { color:#facc15; }
input,select{width:100%;padding:10px;border:2px solid #9face6;border-radius:10px;margin-bottom:15px;font-weight:600;}
body.dark-mode input, body.dark-mode select { background:#475569; border-color:#64748b; color:#f8fafc; }
.form-check-label{font-weight:700;color:#0d47a1;}
body.dark-mode .form-check-label{color:#facc15;}
.form-check-input{transform:scale(1.2);margin-right:10px;cursor:pointer;}
#childrenFields{padding-left:15px;border-left:3px solid #9face6;background:#f0f6fc;margin-bottom:15px;}
body.dark-mode #childrenFields{background:#475569;border-color:#64748b;}
.progress-container{width:100%;background:#e1e9f6;border-radius:20px;height:14px;margin-bottom:25px;box-shadow:inset 0 1px 3px rgb(0 0 0 / 0.1);}
.progress-bar{height:14px;background:#0d47a1;width:0;border-radius:20px;transition:width 0.4s ease;}
.top-buttons{text-align:center;margin-bottom:20px;}
.top-buttons .btn-top{display:inline-block;background:#0d47a1;color:#ffeb3b;font-weight:700;padding:10px 20px;border-radius:12px;margin:0 5px;text-decoration:none;box-shadow:0 4px 12px rgba(13,71,161,0.4);}
.top-buttons .btn-top:hover{background:#074078;box-shadow:0 6px 18px rgba(7,64,120,0.6);}
.step{display:none;animation:fadeIn 0.6s ease forwards;}
.step.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.btn-group{display:flex;justify-content:space-between;margin-top:20px;}
button{padding:12px 25px;font-weight:700;border-radius:12px;border:none;cursor:pointer;flex:1;margin:0 5px;}
.btn-next{background:#0d47a1;color:#ffeb3b;}
.btn-next:hover{background:#074078;}
.btn-prev{background:#9face6;color:#ffeb3b;}
.btn-prev:hover{background:#7a94c3;}
.btn-submit{background:#2e7d32;color:#fff;width:100%;margin-top:20px;}
.btn-submit:hover{background:#1b4f20;}
@media(max-width:480px){.btn-group{flex-direction:column;} .btn-group button{margin:8px 0;}}
#parentName,#displayChildID{font-weight:bold;color:#0d47a1;margin-bottom:10px;}
body.dark-mode #parentName, body.dark-mode #displayChildID{color:#facc15;}
</style>
</head>
<body class="light-mode">

<!-- Header/Nav -->
<header>
    <div class="logo">Ukoo wa Makomelelo</div>
    <button class="nav-toggle" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
    </button>
    <nav class="nav-links">
        <a href="index.php">Nyumbani</a>
        <a href="registration.php">Jisajiri</a>
        <a href="family_tree.php">Ukoo</a>
        <a href="events.php">Matukio</a>
        <a href="contact.php">Mawasiliano</a>
        <?php if($isLoggedIn): ?>
        <a href="logout.php">Toka</a>
        <?php else: ?>
        <a href="login.php">Ingia</a>
        <?php endif; ?>
        <span id="toggleTheme" style="cursor:pointer; font-weight:700;">Dark Mode</span>
    </nav>
</header>

<div class="container">
<h2>Usajili wa Ukoo wa Makomelelo</h2>
<div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>

<form method="post" enctype="multipart/form-data" id="regForm">
<!-- Your multi-step form steps go here exactly as you have them in your previous code -->
</form>
</div>

<script>
// Theme from localStorage
document.addEventListener('DOMContentLoaded', ()=>{
    const savedMode = localStorage.getItem('theme');
    const themeToggle = document.getElementById('toggleTheme');
    if(savedMode==='dark'){
        document.body.classList.replace('light-mode','dark-mode');
        themeToggle.textContent='Light Mode';
    }else{
        document.body.classList.replace('dark-mode','light-mode');
        themeToggle.textContent='Dark Mode';
    }
});

// Navbar toggle
const toggleBtn = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
toggleBtn.addEventListener('click', ()=>{
    toggleBtn.classList.toggle('active');
    navLinks.classList.toggle('show');
});

// Theme toggle with localStorage
const themeToggle = document.getElementById('toggleTheme');
themeToggle.addEventListener('click', ()=>{
    if(document.body.classList.contains('light-mode')){
        document.body.classList.replace('light-mode','dark-mode');
        localStorage.setItem('theme','dark');
        themeToggle.textContent='Light Mode';
    }else{
        document.body.classList.replace('dark-mode','light-mode');
        localStorage.setItem('theme','light');
        themeToggle.textContent='Dark Mode';
    }
});

// Multi-step form logic + children toggle + location dropdowns + parent ID AJAX
// Use exactly your previous JS code for these
</script>
</body>
</html>
