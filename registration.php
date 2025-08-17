<?php
session_start();
include 'config.php';
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
    $district        = $_POST['districtSelect'] ?? '';
    $ward            = $_POST['wardSelect'] ?? '';
    $village         = $_POST['villageSelect'] ?? '';
    $city            = $_POST['city'] ?? '';
    $phone           = $_POST['phone'];
    $email           = $_POST['email'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $parent_id       = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    // Calculate child ID
    if ($parent_id) {
        $res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id=$1 ORDER BY id DESC LIMIT 1", [$parent_id]);
        if ($res_max && pg_num_rows($res_max) > 0) {
            $row_max = pg_fetch_assoc($res_max);
            $last_child_id = (int)$row_max['id'];
            $parent_digits = (string)$parent_id;
            $last_digits = substr($last_child_id, strlen($parent_digits));
            $next_digit = (int)$last_digits + 1;
            if ($next_digit > 999) {
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

    // Photo upload
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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Usajili Ukoo - Makomelelo</title>
<style>
/* Global */
body, html { margin:0; padding:0; font-family:'Segoe UI', sans-serif; transition: background 0.3s,color 0.3s;}
a { text-decoration:none; color: var(--accent);}
a:hover { text-decoration: underline; }
/* Light/Dark Mode Colors */
:root {
  --primary:#0d47a1;
  --secondary:#1976d2;
  --accent:#ffc107;
  --bg-light:#f0f4f8;
  --text-light:#222;
  --bg-dark:#1e293b;
  --text-dark:#f8fafc;
}

/* Light mode */
body.light-mode {
    background: var(--bg-light);
    color: var(--text-light);
}

/* Dark mode */
body.dark-mode {
    background: var(--bg-dark);
    color: var(--text-dark);
}

/* Header */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 25px;
    border-radius: 0 0 15px 15px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    position: relative; z-index: 1000;
}
.logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--accent);
}
.nav-links {
    display: flex;
    gap: 20px;
    align-items: center;
}
.nav-links a {
    color: var(--accent);
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 6px;
    transition: 0.3s;
}
.nav-links a:hover {
    background: var(--accent);
    color: var(--primary);
}

/* Toggle button (hamburger) for small screens */
.nav-toggle {
    display:none;
    flex-direction: column;
    justify-content: space-between;
    width:30px;
    height:24px;
    background:transparent;
    border:none;
    cursor:pointer;
}
.nav-toggle span {
    display:block;
    height:3px;
    background: var(--accent);
    border-radius:2px;
    transition: all 0.4s;
}
.nav-toggle.active span:nth-child(1){
    transform: rotate(45deg) translate(5px,5px);
}
.nav-toggle.active span:nth-child(2){
    opacity: 0;
}
.nav-toggle.active span:nth-child(3){
    transform: rotate(-45deg) translate(5px,-5px);
}

/* Navigation responsive */
@media(max-width:768px){
    .nav-links {
        flex-direction: column;
        position: absolute;
        top: 100%;
        right: 20px;
        background: linear-gradient(180deg,var(--primary), var(--secondary));
        border-radius: 10px;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.35s ease, box-shadow 0.35s;
        z-index: 999;
    }
    .nav-links.show {
        max-height: 500px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .nav-toggle {
        display:flex;
    }
}

/* Container */
.container {
    background:#fff;
    padding:30px 40px;
    border-radius:15px;
    max-width:700px;
    width:100%;
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
    margin: 20px auto 50px auto;
    color: var(--text-light);
}
body.dark-mode .container {
    background:#334155;
    color: var(--text-dark);
}

/* Form headings and labels */
h2 {
    text-align: center;
    color: var(--primary);
    font-weight: 900;
    margin-bottom: 25px;
}
label {
    font-weight: 600;
    color: var(--primary);
    display: block;
    margin-bottom: 6px;
}
input, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 2px solid var(--secondary);
    border-radius: 10px;
    font-weight: 600;
    background: white;
    color: #222;
    transition: border-color 0.3s;
}
body.dark-mode input, body.dark-mode select {
    background: #475569;
    color: var(--text-dark);
    border-color: #64748b;
}
input:focus, select:focus {
    border-color: var(--accent);
    outline: none;
}

/* Form check */
.form-check-label {
    font-weight: 700;
    color: var(--primary);
    cursor:pointer;
}
.form-check-input {
    transform: scale(1.2);
    margin-right: 10px;
    cursor: pointer;
}

/* Children fields styling */
#childrenFields {
    padding-left: 15px;
    border-left: 3px solid var(--secondary);
    background: #f0f6fc;
    margin-bottom: 15px;
}
body.dark-mode #childrenFields {
    background: #475569;
}

/* Progress bar */
.progress-container {
    width: 100%;
    background: #e1e9f6;
    border-radius: 20px;
    height: 14px;
    margin-bottom: 25px;
    box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
}
body.dark-mode .progress-container {
    background: #64748b;
}
.progress-bar {
    height: 14px;
    background: var(--primary);
    width: 0;
    border-radius: 20px;
    transition: width 0.4s ease;
}

/* Steps */
.step {
    display: none;
    animation: fadeIn 0.6s ease forwards;
}
.step.active {
    display: block;
}

/* Buttons */
.btn-group {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
button {
    padding: 12px 25px;
    font-weight: 700;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    flex: 1;
    margin: 0 5px;
}
.btn-next {
    background: var(--primary);
    color: #fff;
}
.btn-next:hover {
    background: #07327a;
}
.btn-prev {
    background: var(--secondary);
    color: #fff;
}
.btn-prev:hover {
    background: #1456d4;
}
.btn-submit {
    background: #1b5e20;
    color: #fff;
    width: 100%;
    margin-top: 20px;
    border-radius: 15px;
    padding: 12px;
}
.btn-submit:hover {
    background: #143d12;
}

/* Text for parent name and child id */
#parentName, #displayChildID {
    font-weight: bold;
    color: var(--primary);
    margin-bottom: 10px;
}

/* Fade in animation */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body class="light-mode">

<!-- Header -->
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
        <span id="toggleTheme" style="cursor:pointer; font-weight:700; margin-left:15px;">Dark Mode</span>
    </nav>
</header>

<!-- Registration Container -->
<div class="container">
    <h2>Usajili wa Ukoo wa Makomelelo</h2>
    <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>
    <form method="post" enctype="multipart/form-data" id="regForm">
      <!-- Step 1 -->
      <div class="step active">
        <label>Jina la Kwanza *</label>
        <input type="text" name="first_name" required>
        <label>Jina la Kati</label>
        <input type="text" name="middle_name">
        <label>Jina la Mwisho *</label>
        <input type="text" name="last_name" required>
      </div>
      <!-- Step 2 -->
      <div class="step">
        <label>Tarehe ya Kuzaliwa *</label>
        <input type="date" name="dob" required>
        <label>Jinsia *</label>
        <select name="gender" required>
          <option value="" disabled selected>--Chagua--</option>
          <option value="male">Mwanaume</option>
          <option value="female">Mwanamke</option>
        </select>
        <label>Hali ya Ndoa *</label>
        <select name="marital_status" required>
          <option value="" disabled selected>--Chagua--</option>
          <option value="single">Sijaoa/Sijaolewa</option>
          <option value="married">Nimeoa/Nimeolewa</option>
        </select>
        <div class="form-check">
          <input type="checkbox" name="has_children" id="hasChildren" class="form-check-input">
          <label class="form-check-label" for="hasChildren">Una Watoto?</label>
        </div>
        <div id="childrenFields" style="display:none;">
          <label>Idadi ya Watoto wa Kiume</label>
          <input type="number" name="children_male" min="0" value="0">
          <label>Idadi ya Watoto wa Kike</label>
          <input type="number" name="children_female" min="0" value="0">
        </div>
      </div>
      <!-- Step 3 -->
      <div class="step">
        <label>Nchi</label>
        <select name="country" id="countrySelect" required>
          <option value="Tanzania">Tanzania</option>
          <option value="Other">Nyingine</option>
        </select>
        <label>Mkoa</label>
        <select name="region" id="regionSelect" required></select>
        <label>Wilaya</label>
        <select name="districtSelect" id="districtSelect" required></select>
        <label>Kata</label>
        <select name="wardSelect" id="wardSelect" required></select>
        <label>Kijiji/Mtaa</label>
        <select name="villageSelect" id="villageSelect" required></select>
      </div>
      <!-- Step 4 -->
      <div class="step">
        <label>Namba ya Simu *</label>
        <input type="text" name="phone" required>
        <label>Email *</label>
        <input type="email" name="email" required>
        <label>Password *</label>
        <input type="password" name="password" required>
      </div>
      <!-- Step 5 -->
      <div class="step">
        <label>ID ya Mzazi (Parent ID)</label>
        <input type="number" name="parent_id" id="parent_id">
        <div id="parentName"></div>
        <div id="displayChildID">ID ya mtoto itakuwa: <span id="childID">1</span></div>
        <label>Picha</label>
        <input type="file" name="photo" accept="image/*">
      </div>
      <div class="btn-group">
        <button type="button" id="prevBtn" class="btn-prev" disabled>&larr; Nyuma</button>
        <button type="button" id="nextBtn" class="btn-next">Mbele &rarr;</button>
      </div>
      <button type="submit" class="btn-submit" style="display:none;">Sajili</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Dark/Light mode toggle and save to localStorage
document.addEventListener('DOMContentLoaded', () => {
  const savedMode = localStorage.getItem('theme');
  const body = document.body;
  const toggleTheme = document.getElementById('toggleTheme');

  function applyMode(mode) {
    if (mode === 'dark') {
      body.classList.add('dark-mode');
      body.classList.remove('light-mode');
      toggleTheme.textContent = 'Light Mode';
    } else {
      body.classList.add('light-mode');
      body.classList.remove('dark-mode');
      toggleTheme.textContent = 'Dark Mode';
    }
  }

  applyMode(savedMode || 'light');

  toggleTheme.addEventListener('click', () => {
    if (body.classList.contains('light-mode')) {
      applyMode('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      applyMode('light');
      localStorage.setItem('theme', 'light');
    }
  });
});

// Navbar toggle for small screens
const toggleBtn = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
toggleBtn.addEventListener('click', () => {
  toggleBtn.classList.toggle('active');
  navLinks.classList.toggle('show');
});

// Multi-step form logic
let currentStep = 0;
const steps = $(".step"),
      progressBar = $("#progressBar"),
      prevBtn = $("#prevBtn"),
      nextBtn = $("#nextBtn"),
      submitBtn = $(".btn-submit");

function showStep(n) {
  steps.removeClass("active").eq(n).addClass("active");
  prevBtn.prop("disabled", n === 0);
  if (n === steps.length - 1) {
    nextBtn.hide();
    submitBtn.show();
  } else {
    nextBtn.show();
    submitBtn.hide();
  }
  progressBar.css("width", ((n + 1) / steps.length * 100) + "%");
}
showStep(currentStep);

nextBtn.click(() => {
  if (validateStep()) {
    currentStep++;
    if (currentStep >= steps.length) currentStep = steps.length - 1;
    showStep(currentStep);
  }
});
prevBtn.click(() => {
  currentStep--;
  if (currentStep < 0) currentStep = 0;
  showStep(currentStep);
});

function validateStep() {
  let valid = true;
  steps.eq(currentStep).find("input,select").each(function () {
    if ($(this).prop("required") && $(this).val() === "") {
      alert("Tafadhali jaza " + $(this).prev("label").text());
      valid = false;
      return false; // Break loop
    }
  });
  return valid;
}

// Toggle children fields
$("#hasChildren").change(function () {
  $("#childrenFields").toggle(this.checked);
});

// Location dropdown data and handlers
let locData = {};
$.getJSON('tanzania_locations.json', function (data) {
  locData = data;
  fillRegions();
});

function fillRegions() {
  let r = $("#regionSelect");
  r.html('<option value="">--Chagua Mkoa--</option>');
  for (let region in locData) {
    r.append(`<option value="${region}">${region}</option>`);
  }
  $("#districtSelect").html('<option value="">--Chagua Wilaya--</option>');
  $("#wardSelect").html('<option value="">--Chagua Kata--</option>');
  $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>');
}
function fillDistricts() {
  let reg = $("#regionSelect").val();
  let d = $("#districtSelect");
  d.html('<option value="">--Chagua Wilaya--</option>');
  if (reg && locData[reg]) {
    for (let district in locData[reg]) {
      d.append(`<option value="${district}">${district}</option>`);
    }
  }
  fillWard();
}
function fillWard() {
  let reg = $("#regionSelect").val();
  let dis = $("#districtSelect").val();
  let w = $("#wardSelect");
  w.html('<option value="">--Chagua Kata--</option>');
  if (reg && dis && locData[reg][dis]) {
    for (let ward in locData[reg][dis]) {
      w.append(`<option value="${ward}">${ward}</option>`);
    }
  }
  fillVillage();
}
function fillVillage() {
  let reg = $("#regionSelect").val();
  let dis = $("#districtSelect").val();
  let ward = $("#wardSelect").val();
  let v = $("#villageSelect");
  v.html('<option value="">--Chagua Kijiji/Mtaa--</option>');
  if (reg && dis && ward && locData[reg][dis][ward]) {
    locData[reg][dis][ward].forEach(function (vi) {
      v.append(`<option value="${vi}">${vi}</option>`);
    });
  }
}
$("#regionSelect").change(fillDistricts);
$("#districtSelect").change(fillWard);
$("#wardSelect").change(fillVillage);

// AJAX Parent info
$("#parent_id").on("input", function () {
  let pid = $(this).val();
  if (pid === '') {
    $("#parentName").text('');
    $("#childID").text('1');
    return;
  }
  $.post('get_parent_info.php', { parent_id: pid }, function (data) {
    try {
      let obj = JSON.parse(data);
      if (obj.error) {
        $("#parentName").text(obj.error);
        $("#childID").text('Error');
      } else {
        $("#parentName").text('Mzazi: ' + obj.name);
        $("#childID").text(obj.next_child_id);
      }
    } catch (e) {
      $("#parentName").text('Tatizo la server');
      $("#childID").text('Error');
    }
  });
});
</script>
</body>
</html>
