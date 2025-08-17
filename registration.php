<?php
include 'config.php';

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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usajili Ukoo - Makomelelo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
:root{
  --primary:#1a237e;      /* Dark blue */
  --secondary:#7986cb;    /* Soft blue */
  --accent:#ffc107;       /* Amber */
  --bg:linear-gradient(135deg,#e3f2fd 0%,#c5cae9 100%);
}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:20px;min-height:100vh;display:flex;flex-direction:column;align-items:center;}
.navbar{display:flex;justify-content:space-between;align-items:center;padding:10px 20px;background:var(--primary);border-radius:12px;width:100%;max-width:900px;margin-bottom:20px;position:relative;}
.navbar a{color:var(--accent);text-decoration:none;font-weight:700;margin:0 10px;transition:color 0.3s;}
.navbar a:hover{color:#fff;}
.navbar .nav-right button{
    padding:8px 12px;
    border:none;
    border-radius:50%;
    background:var(--accent);
    color:var(--primary);
    font-weight:700;
    cursor:pointer;
    position:relative;
    transition: transform 0.3s ease;
}
.navbar .nav-right button.active::before{
    content: "✖";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.nav-left{display:flex;gap:15px;}
.nav-left.show{display:flex; flex-direction:column; position:absolute; top:60px; left:0; width:100%; background:var(--primary); padding:10px 0; border-radius:0;}
.container{background:#fff;padding:30px 40px;border-radius:15px;max-width:700px;width:100%;box-shadow:0 20px 40px rgba(0,0,0,0.15);}
h2{text-align:center;color:var(--primary);margin-bottom:25px;font-weight:900;}
label{font-weight:600;color:var(--primary);}
input,select{width:100%;padding:10px;border:2px solid var(--secondary);border-radius:10px;margin-bottom:15px;font-weight:600;}
.form-check-label{font-weight:700;color:var(--primary);}
.form-check-input{transform:scale(1.2);margin-right:10px;cursor:pointer;}
#childrenFields{padding-left:15px;border-left:3px solid var(--secondary);background:#f0f6fc;margin-bottom:15px;}
.progress-container{width:100%;background:#e1e9f6;border-radius:20px;height:14px;margin-bottom:25px;box-shadow:inset 0 1px 3px rgb(0 0 0 / 0.1);}
.progress-bar{height:14px;background:var(--primary);width:0;border-radius:20px;transition:width 0.4s ease;}
.step{display:none;animation:fadeIn 0.6s ease forwards;}
.step.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.btn-group{display:flex;justify-content:space-between;margin-top:20px;}
button{padding:12px 25px;font-weight:700;border-radius:12px;border:none;cursor:pointer;flex:1;margin:0 5px;}
.btn-next{background:var(--primary);color:#fff;}
.btn-next:hover{background:#0d1b66;}
.btn-prev{background:var(--secondary);color:#fff;}
.btn-prev:hover{background:#5c6bc0;}
.btn-submit{background:#1b5e20;color:#fff;width:100%;margin-top:20px;}
.btn-submit:hover{background:#143d12;}
@media(max-width:768px){
  .nav-left{display:none;}
  .nav-left.show{display:flex;flex-direction:column;gap:10px;padding:10px;}
}
#parentName,#displayChildID{font-weight:bold;color:var(--primary);margin-bottom:10px;}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-left">
    <a href="index.php">Nyumbani</a>
    <a href="Registration.php">Jisajiri</a>
    <a href="family_tree.php">Ukoo</a>
    <a href="events.php">Matukio</a>
    <a href="contact.php">Mawasiliano</a>
  </div>
  <div class="nav-right">
    <button id="navToggle"></button>
  </div>
</nav>

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

<script>
// Multi-step form
let currentStep = 0;
const steps = $(".step"), progressBar = $("#progressBar");
function showStep(n){
    steps.removeClass("active").eq(n).addClass("active");
    $("#prevBtn").prop("disabled", n===0);
    if(n===steps.length-1){$("#nextBtn").hide();$(".btn-submit").show();}
    else{$("#nextBtn").show();$(".btn-submit").hide();}
    progressBar.css("width",((n+1)/steps.length*100)+"%");
}
$("#nextBtn").click(function(){ if(validateStep()) {currentStep++; if(currentStep>=steps.length) currentStep=steps.length-1; showStep(currentStep);} });
$("#prevBtn").click(function(){currentStep--; if(currentStep<0) currentStep=0; showStep(currentStep);});
function validateStep(){
    let valid = true;
    steps.eq(currentStep).find("input,select").each(function(){ if($(this).prop("required") && $(this).val()===""){ alert("Tafadhali jaza "+$(this).prev("label").text()); valid=false; return false;} });
    return valid;
}

// Children toggle
$("#hasChildren").change(function(){ $("#childrenFields").toggle(this.checked); });

// Location dropdowns
let locData = {};
$.getJSON('tanzania_locations.json', function(data){ locData = data; fillRegions(); });
function fillRegions(){ let r=$("#regionSelect"); r.html('<option value="">--Chagua Mkoa--</option>'); for(let region in locData){ r.append(`<option value="${region}">${region}</option>`); } $("#districtSelect").html('<option value="">--Chagua Wilaya--</option>'); $("#wardSelect").html('<option value="">--Chagua Kata--</option>'); $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>'); }
function fillDistricts(){ let reg=$("#regionSelect").val(); let d=$("#districtSelect"); d.html('<option value="">--Chagua Wilaya--</option>'); if(reg && locData[reg]){ for(let district in locData[reg]){ d.append(`<option value="${district}">${district}</option>`); } } fillWard(); }
function fillWard(){ let reg=$("#regionSelect").val(); let dis=$("#districtSelect").val(); let w=$("#wardSelect"); w.html('<option value="">--Chagua Kata--</option>'); if(reg && dis && locData[reg][dis]){ for(let ward in locData[reg][dis]){ w.append(`<option value="${ward}">${ward}</option>`); } } fillVillage(); }
function fillVillage(){ let reg=$("#regionSelect").val(); let dis=$("#districtSelect").val(); let ward=$("#wardSelect").val(); let v=$("#villageSelect"); v.html('<option value="">--Chagua Kijiji/Mtaa--</option>'); if(reg && dis && ward && locData[reg][dis][ward]){ locData[reg][dis][ward].forEach(function(vi){ v.append(`<option value="${vi}">${vi}</option>`); }); } }
$("#regionSelect").change(fillDistricts);
$("#districtSelect").change(fillWard);
$("#wardSelect").change(fillVillage);

// AJAX Parent info
$("#parent_id").on("input",function(){
let pid=$(this).val();
if(pid===''){ $("#parentName").text(''); $("#childID").text('1'); return; }
$.post('get_parent_info.php',{parent_id:pid},function(data){
try{
    let obj=JSON.parse(data);
    if(obj.error){$("#parentName").text(obj.error);$("#childID").text('Error');}
    else {$("#parentName").text('Mzazi: '+obj.name);$("#childID").text(obj.next_child_id);}
}catch(e){$("#parentName").text('Tatizo la server');$("#childID").text('Error');}
});
});

// Navbar toggle
$("#navToggle").click(function(){
  $(this).toggleClass("active");
  $(".nav-left").toggleClass("show");
});
</script>
</body>
</html>
