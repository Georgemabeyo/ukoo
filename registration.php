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

    $photo = '';
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo);
    }

    $sql = "INSERT INTO family_tree (
        first_name, middle_name, last_name, dob, gender, marital_status,
        has_children, children_male, children_female, country, region, district,
        ward, village, city, phone, email, password, photo, parent_id
    ) VALUES (
        $1, $2, $3, $4, $5, $6,
        $7, $8, $9, $10, $11, $12,
        $13, $14, $15, $16, $17, $18, $19, $20
    )";
    $params = [
        $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
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
<title>Usajili - Ukoo wa Makomelelo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
body {
    background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.container {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    max-width: 650px;
    width: 100%;
    padding: 30px 40px 40px;
}
h2 {
    color: #0d47a1;
    font-weight: 900;
    text-align: center;
    margin-bottom: 30px;
    letter-spacing: 2px;
}

/* Top buttons */
.top-buttons {
    text-align: center;
    margin-bottom: 20px;
}
.top-buttons .btn-top {
    display: inline-block;
    background-color: #0d47a1;
    color: #ffeb3b;
    font-weight: 700;
    padding: 12px 25px;
    border-radius: 12px;
    margin: 0 10px;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(13,71,161,0.4);
    transition: background 0.3s ease, box-shadow 0.3s ease;
}
.top-buttons .btn-top:hover,
.top-buttons .btn-top:focus {
    background-color: #074078;
    box-shadow: 0 6px 18px rgba(7,64,120,0.6);
    outline: none;
}

form {
    position: relative;
}
.step {
    display: none;
    animation: fadeIn 0.6s ease forwards;
}
.step.active {
    display: block;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 8px;
}
input[type=text], input[type=email], input[type=password], input[type=number], input[type=date], select, input[type=file] {
    width: 100%;
    padding: 12px 15px;
    border: 2.5px solid #9face6;
    border-radius: 10px;
    font-size: 1.1rem;
    outline-offset: 4px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.07);
    font-weight: 600;
}
input:focus, select:focus, input[type=file]:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 10px #0d47a1aa;
}
.form-check-label { font-weight: 700; color: #0d47a1; user-select:none; }
.form-check-input { transform: scale(1.2); margin-right:12px; cursor:pointer; }

#childrenFields {
    margin-left: 20px;
    border-left: 3px solid #9face6;
    padding-left: 20px;
    margin-top: 15px;
    background: #f0f6fc;
    border-radius: 10px;
}

.progress-container { width: 100%; background: #e1e9f6; border-radius: 20px; height: 14px; margin-bottom: 40px; box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);}
.progress-bar { height: 14px; background: #0d47a1; width: 0; border-radius: 20px; transition: width 0.4s ease; }

.btn-group { display: flex; justify-content: space-between; margin-top: 30px; }
button { padding: 12px 30px; font-weight: 700; border-radius: 12px; border:none; font-size: 1.15rem; cursor:pointer; box-shadow:0 4px 12px rgba(13,71,161,0.4); transition: background-color 0.3s ease, box-shadow 0.3s ease; flex:1; margin:0 5px; }
button:disabled { opacity:0.5; cursor:not-allowed; box-shadow:none; }

.btn-next { background-color: #0d47a1; color: #ffeb3b; }
.btn-next:hover:not(:disabled),
.btn-next:focus:not(:disabled) { background-color: #074078; box-shadow: 0 6px 18px #074078aa; }

.btn-prev { background-color: #9face6; color: #ffeb3b; }
.btn-prev:hover:not(:disabled),
.btn-prev:focus:not(:disabled) { background-color: #7a94c3; box-shadow: 0 6px 18px #7a94c3aa; }

.btn-submit { background-color: #2e7d32; color:white; margin-top:20px; width:100%; box-shadow:0 6px 22px #2e7d3299; }
.btn-submit:hover, .btn-submit:focus { background-color: #1b4f20; box-shadow:0 8px 28px #1b4f2099; }

@media(max-width:480px){
    button { font-size:1rem; padding:10px 20px; }
    .btn-group { flex-direction: column; }
    .btn-group button { margin: 8px 0; }
    #childrenFields { margin-left: 10px; padding-left: 15px; }
}
</style>
</head>
<body>
<div class="container" role="main">

    <div class="top-buttons">
        <a href="index.php" class="btn-top">Nyumbani</a>
        <a href="registration.php" class="btn-top">Usajili wa Ukoo</a>
    </div>

    <h2>Usajili wa Ukoo wa Makomelelo</h2>

    <div class="progress-container" aria-hidden="true">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <form method="post" enctype="multipart/form-data" id="registrationForm" novalidate>

        <!-- Step 1 -->
        <div class="step active">
            <label for="first_name">Jina la Kwanza *</label>
            <input type="text" id="first_name" name="first_name" required />
            <label for="middle_name">Jina la Kati</label>
            <input type="text" id="middle_name" name="middle_name" />
            <label for="last_name">Jina la Mwisho *</label>
            <input type="text" id="last_name" name="last_name" required />
        </div>

        <!-- Step 2 -->
        <div class="step">
            <label for="dob">Tarehe ya Kuzaliwa *</label>
            <input type="date" id="dob" name="dob" required />
            <label for="gender">Jinsia *</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>--Chagua--</option>
                <option value="male">Mwanaume</option>
                <option value="female">Mwanamke</option>
            </select>
            <label for="marital_status">Hali ya Ndoa *</label>
            <select id="marital_status" name="marital_status" required>
                <option value="" disabled selected>--Chagua--</option>
                <option value="single">Hajaoa/Hajaolewa</option>
                <option value="married">Kaoa/Ameolewa</option>
            </select>
            <div class="form-check" style="margin-top:15px;">
                <input type="checkbox" id="hasChildren" name="has_children" class="form-check-input" />
                <label for="hasChildren" class="form-check-label">Ana Watoto?</label>
            </div>
            <div id="childrenFields" style="display:none;">
                <label for="children_male">Idadi ya Watoto wa Kiume</label>
                <input type="number" id="children_male" name="children_male" min="0" value="0" />
                <label for="children_female">Idadi ya Watoto wa Kike</label>
                <input type="number" id="children_female" name="children_female" min="0" value="0" />
            </div>
        </div>

        <!-- Step 3 -->
        <div class="step">
            <label for="country">Nchi *</label>
            <select id="country" name="country" required>
                <option value="Tanzania" selected>Tanzania</option>
                <option value="Kenya">Kenya</option>
                <option value="Uganda">Uganda</option>
                <option value="Other">Nyingine</option>
            </select>
            <div id="tz-fields">
                <label for="region">Mkoa *</label>
                <select id="region" name="region" required></select>
                <label for="district">Wilaya *</label>
                <select id="district" name="district" required></select>
                <label for="ward">Kata *</label>
                <select id="ward" name="ward" required></select>
                <label for="village">Kijiji/Mtaa *</label>
                <select id="village" name="village" required></select>
            </div>
            <div id="other-country" style="display:none; margin-top:10px;">
                <label for="city">Mji/Jiji</label>
                <input type="text" id="city" name="city" placeholder="Andika Mji au Jiji" />
            </div>
        </div>

        <!-- Step 4 -->
        <div class="step">
            <label for="phone">Namba ya Simu *</label>
            <input type="text" id="phone" name="phone" required placeholder="Andika namba ya simu" />
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required placeholder="Andika barua pepe" />
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required placeholder="Weka nenosiri" />
            <label for="parent_id">Mzazi (Parent ID)</label>
            <input type="number" id="parent_id" name="parent_id" placeholder="Weka ID ya mzazi kama ipo" />
            <label for="photo">Picha</label>
            <input type="file" id="photo" name="photo" accept="image/*" />
        </div>

        <div class="btn-group">
            <button type="button" id="prevBtn" class="btn-prev" disabled>&larr; Nyuma</button>
            <button type="button" id="nextBtn" class="btn-next">Mbele &rarr;</button>
        </div>
        <button type="submit" id="submitBtn" class="btn-submit" style="display:none;">Sajili</button>
    </form>
</div>

<script>
// --- JS for Tanzania regions ---
const data = {
    "Dar es Salaam": {
        "Ilala": { "Upanga East":["Msasani","Kivukoni"], "Mchikichini":["Mtaa A","Mtaa B"] },
        "Kinondoni": { "Kibamba":["Kijiji A","Kijiji B"], "Magomeni":["Mtaa C","Mtaa D"] }
    },
    "Dodoma": {
        "Dodoma Urban": { "Hombolo":["Kijiji 1","Kijiji 2"], "Tambukareli":["Kijiji 3","Kijiji 4"] },
        "Bahi": { "Bahi":["Kijiji 5","Kijiji 6"], "Chali":["Kijiji 7","Kijiji 8"] }
    }
};
const countrySelect=document.getElementById("country"),
      regionSelect=document.getElementById("region"),
      districtSelect=document.getElementById("district"),
      wardSelect=document.getElementById("ward"),
      villageSelect=document.getElementById("village"),
      tzFields=document.getElementById("tz-fields"),
      otherCountryFields=document.getElementById("other-country"),
      hasChildrenCheckbox=document.getElementById("hasChildren"),
      childrenFields=document.getElementById("childrenFields");

function fillRegions(){
    regionSelect.innerHTML='<option value="">--Chagua Mkoa--</option>';
    for(let region in data) regionSelect.innerHTML+=`<option value="${region}">${region}</option>`;
    districtSelect.innerHTML='<option value="">--Chagua Wilaya--</option>';
    wardSelect.innerHTML='<option value="">--Chagua Kata--</option>';
    villageSelect.innerHTML='<option value="">--Chagua Kijiji/Mtaa--</option>';
}

regionSelect.addEventListener('change',()=>{
    const sel=regionSelect.value;
    districtSelect.innerHTML='<option value="">--Chagua Wilaya--</option>';
    wardSelect.innerHTML='<option value="">--Chagua Kata--</option>';
    villageSelect.innerHTML='<option value="">--Chagua Kijiji/Mtaa--</option>';
    if(sel&&data[sel]) for(let d in data[sel]) districtSelect.innerHTML+=`<option value="${d}">${d}</option>`;
});

districtSelect.addEventListener('change',()=>{
    const selRegion=regionSelect.value, selDistrict=districtSelect.value;
    wardSelect.innerHTML='<option value="">--Chagua Kata--</option>';
    villageSelect.innerHTML='<option value="">--Chagua Kijiji/Mtaa--</option>';
    if(selRegion&&selDistrict&&data[selRegion][selDistrict])
        for(let w in data[selRegion][selDistrict]) wardSelect.innerHTML+=`<option value="${w}">${w}</option>`;
});

wardSelect.addEventListener('change',()=>{
    const selRegion=regionSelect.value, selDistrict=districtSelect.value, selWard=wardSelect.value;
    villageSelect.innerHTML='<option value="">--Chagua Kijiji/Mtaa--</option>';
    if(selRegion&&selDistrict&&selWard&&data[selRegion][selDistrict][selWard])
        for(let v of data[selRegion][selDistrict][selWard]) villageSelect.innerHTML+=`<option value="${v}">${v}</option>`;
});

countrySelect.addEventListener('change',()=>{
    if(countrySelect.value==="Tanzania"){ tzFields.style.display="block"; otherCountryFields.style.display="none"; fillRegions(); }
    else{ tzFields.style.display="none"; otherCountryFields.style.display="block"; }
});
fillRegions();

// --- JS for children ---
hasChildrenCheckbox.addEventListener('change',()=>{childrenFields.style.display=hasChildrenCheckbox.checked?'block':'none';});

// --- Multi-step form ---
let currentStep=0;
const steps=document.querySelectorAll(".step"),
      nextBtn=document.getElementById("nextBtn"),
      prevBtn=document.getElementById("prevBtn"),
      submitBtn=document.getElementById("submitBtn"),
      progressBar=document.getElementById("progressBar");

function showStep(n){
    steps.forEach((s,i)=>s.classList.toggle("active",i===n));
    prevBtn.disabled=n===0;
    if(n===steps.length-1){nextBtn.style.display="none"; submitBtn.style.display="block";}
    else{nextBtn.style.display="inline-block"; submitBtn.style.display="none";}
    progressBar.style.width=((n+1)/steps.length*100)+"%";
}

nextBtn.addEventListener('click',()=>{
    if(!validateForm()) return;
    currentStep++; if(currentStep>=steps.length) currentStep=steps.length-1; showStep(currentStep);
});
prevBtn.addEventListener('click',()=>{currentStep--; if(currentStep<0) currentStep=0; showStep(currentStep);});

function validateForm(){
    const inputs=steps[currentStep].querySelectorAll("input,select");
    for(let inp of inputs){ if(inp.hasAttribute("required") && !inp.value){ alert("Tafadhali jaza "+inp.previousElementSibling.innerText); return false; } }
    return true;
}
</script>
</body>
</html>
