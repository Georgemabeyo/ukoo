<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usajili Ukoo - Makomelelo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    background: #f2f6fc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.container {
    max-width: 700px;
    background: #fff;
    padding: 30px;
    margin: 40px auto;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}
label {
    font-weight: 500;
    margin-top: 10px;
}
input, select {
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 15px;
}
input[type="file"] {
    padding: 3px;
}
.step {
    display: none;
}
.step.active {
    display: block;
}
.btn-group {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
.btn-prev, .btn-next, .btn-submit {
    padding: 10px 25px;
    border-radius: 8px;
}
.progress-container {
    width: 100%;
    background-color: #e9ecef;
    border-radius: 8px;
    margin-bottom: 20px;
    height: 10px;
}
.progress-bar {
    height: 10px;
    background-color: #0d6efd;
    width: 0%;
    border-radius: 8px;
}
#childrenFields {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 10px;
    margin-top: 10px;
}
#parentName, #displayChildID {
    margin-top: 5px;
    font-weight: 500;
    color: #0d6efd;
}
</style>
</head>
<body>

<div class="container">
    <h2>Usajili wa Ukoo wa Makomelelo</h2>
    <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>

    <form method="post" enctype="multipart/form-data" id="regForm">

    <!-- Step 1 -->
    <div class="step active">
        <label>Jina la Kwanza *</label>
        <input type="text" name="first_name" class="form-control" required>
        <label>Jina la Kati</label>
        <input type="text" name="middle_name" class="form-control">
        <label>Jina la Mwisho *</label>
        <input type="text" name="last_name" class="form-control" required>
    </div>

    <!-- Step 2 -->
    <div class="step">
        <label>Tarehe ya Kuzaliwa *</label>
        <input type="date" name="dob" class="form-control" required>
        <label>Jinsia *</label>
        <select name="gender" class="form-select" required>
            <option value="" disabled selected>--Chagua--</option>
            <option value="male">Mwanaume</option>
            <option value="female">Mwanamke</option>
        </select>
        <label>Hali ya Ndoa *</label>
        <select name="marital_status" class="form-select" required>
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
            <input type="number" name="children_male" class="form-control" min="0" value="0">
            <label>Idadi ya Watoto wa Kike</label>
            <input type="number" name="children_female" class="form-control" min="0" value="0">
        </div>
    </div>

    <!-- Step 3: Location -->
    <div class="step">
        <label>Nchi</label>
        <select name="country" id="countrySelect" class="form-select" required>
            <option value="Tanzania">Tanzania</option>
            <option value="Other">Nyingine</option>
        </select>

        <label>Mkoa</label>
        <select name="region" id="regionSelect" class="form-select" required></select>
        <label>Wilaya</label>
        <select name="district" id="districtSelect" class="form-select" required></select>
        <label>Kata</label>
        <select name="ward" id="wardSelect" class="form-select" required></select>
        <label>Kijiji/Mtaa</label>
        <select name="village" id="villageSelect" class="form-select" required></select>
    </div>

    <!-- Step 4 -->
    <div class="step">
        <label>Namba ya Simu *</label>
        <input type="text" name="phone" class="form-control" required>
        <label>Email *</label>
        <input type="email" name="email" class="form-control" required>
        <label>Password *</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <!-- Step 5 -->
    <div class="step">
        <label>ID ya Mzazi (Parent ID)</label>
        <input type="number" name="parent_id" id="parent_id" class="form-control">
        <div id="parentName"></div>
        <div id="displayChildID">ID ya mtoto itakuwa: <span id="childID">1</span></div>
        <label>Picha</label>
        <input type="file" name="photo" accept="image/*" class="form-control">
    </div>

    <div class="btn-group">
        <button type="button" id="prevBtn" class="btn btn-secondary" disabled>&larr; Nyuma</button>
        <button type="button" id="nextBtn" class="btn btn-primary">Mbele &rarr;</button>
    </div>
    <button type="submit" class="btn btn-success btn-submit" style="display:none; margin-top:15px;">Sajili</button>

    </form>
</div>

<script>
// Multi-step form logic
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
function validateStep(){let valid = true; steps.eq(currentStep).find("input,select").each(function(){ if($(this).prop("required") && $(this).val()===""){ alert("Tafadhali jaza "+$(this).prev("label").text()); valid=false; return false;} }); return valid;}

// Children toggle
$("#hasChildren").change(function(){ $("#childrenFields").toggle(this.checked); });

// Dynamic Location dropdowns
function fillRegions(){
    $.get('get_locations.php', {level:'region'}, function(data){
        $("#regionSelect").html('<option value="">--Chagua Mkoa--</option>'+data);
        $("#districtSelect").html('<option value="">--Chagua Wilaya--</option>');
        $("#wardSelect").html('<option value="">--Chagua Kata--</option>');
        $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>');
    });
}
$("#regionSelect").change(function(){
    let region = $(this).val();
    $.get('get_locations.php', {level:'district', region:region}, function(data){
        $("#districtSelect").html('<option value="">--Chagua Wilaya--</option>'+data);
        $("#wardSelect").html('<option value="">--Chagua Kata--</option>');
        $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>');
    });
});
$("#districtSelect").change(function(){
    let district = $(this).val();
    $.get('get_locations.php', {level:'ward', district:district}, function(data){
        $("#wardSelect").html('<option value="">--Chagua Kata--</option>'+data);
        $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>');
    });
});
$("#wardSelect").change(function(){
    let ward = $(this).val();
    $.get('get_locations.php', {level:'village', ward:ward}, function(data){
        $("#villageSelect").html('<option value="">--Chagua Kijiji/Mtaa--</option>'+data);
    });
});
fillRegions();

// AJAX Parent info
$("#parent_id").on("input",function(){
let pid=$(this).val();
if(pid===''){ $("#parentName").text(''); $("#childID").text('1'); return; }
$.post('get_parent_info.php',{parent_id:pid},function(data){
try{let obj=JSON.parse(data); if(obj.error){$("#parentName").text(obj.error);$("#childID").text('Error');} else {$("#parentName").text('Mzazi: '+obj.name);$("#childID").text(obj.next_child_id);}}catch(e){$("#parentName").text('Tatizo la server');$("#childID").text('Error');}
});
});
</script>
</body>
</html>
