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

    // Calculate child ID if parent_id exists
    if($parent_id){
        // Get last child
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
            $new_id = (int)($parent_id . '1'); // First child
        }
    } else {
        // If no parent, this is the root founder
        $new_id = 1;
    }

    // Handle photo
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
<title>Usajili - Ukoo wa Makomelelo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body{background:linear-gradient(120deg,#74ebd5 0%,#9face6 100%);font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;padding:20px;min-height:100vh;display:flex;justify-content:center;align-items:center;}
.container{background:#fff;padding:30px 40px;border-radius:15px;max-width:650px;width:100%;box-shadow:0 20px 40px rgba(0,0,0,0.15);}
h2{text-align:center;color:#0d47a1;margin-bottom:30px;font-weight:900;}
input,select{width:100%;padding:10px;border:2px solid #9face6;border-radius:10px;margin-bottom:15px;font-weight:600;}
#parentName{font-weight:bold;color:#0d47a1;margin-bottom:10px;}
#displayChildID{font-weight:bold;color:#0d47a1;margin-bottom:15px;}
</style>
</head>
<body>
<div class="container">
<h2>Usajili wa Ukoo wa Makomelelo</h2>
<form method="post" enctype="multipart/form-data">

<label for="first_name">Jina la Kwanza *</label>
<input type="text" name="first_name" id="first_name" required>

<label for="middle_name">Jina la Kati</label>
<input type="text" name="middle_name" id="middle_name">

<label for="last_name">Jina la Mwisho *</label>
<input type="text" name="last_name" id="last_name" required>

<label for="dob">Tarehe ya Kuzaliwa *</label>
<input type="date" name="dob" id="dob" required>

<label for="gender">Jinsia *</label>
<select name="gender" id="gender" required>
<option value="" disabled selected>--Chagua--</option>
<option value="male">Mwanaume</option>
<option value="female">Mwanamke</option>
</select>

<label for="marital_status">Hali ya Ndoa *</label>
<select name="marital_status" id="marital_status" required>
<option value="" disabled selected>--Chagua--</option>
<option value="single">Hajaoa/Hajaolewa</option>
<option value="married">Kaoa/Ameolewa</option>
</select>

<div class="form-check">
<input type="checkbox" id="hasChildren" name="has_children" class="form-check-input">
<label for="hasChildren" class="form-check-label">Ana Watoto?</label>
</div>
<div id="childrenFields" style="display:none;">
<label for="children_male">Idadi ya Watoto wa Kiume</label>
<input type="number" name="children_male" id="children_male" min="0" value="0">
<label for="children_female">Idadi ya Watoto wa Kike</label>
<input type="number" name="children_female" id="children_female" min="0" value="0">
</div>

<label for="country">Nchi *</label>
<select name="country" id="country" required>
<option value="Tanzania" selected>Tanzania</option>
<option value="Kenya">Kenya</option>
<option value="Uganda">Uganda</option>
<option value="Other">Nyingine</option>
</select>

<label for="region">Mkoa</label>
<input type="text" name="region" id="region">
<label for="district">Wilaya</label>
<input type="text" name="district" id="district">
<label for="ward">Kata</label>
<input type="text" name="ward" id="ward">
<label for="village">Kijiji/Mtaa</label>
<input type="text" name="village" id="village">
<label for="city">Mji/Jiji</label>
<input type="text" name="city" id="city">

<label for="phone">Namba ya Simu *</label>
<input type="text" name="phone" id="phone" required>

<label for="email">Email *</label>
<input type="email" name="email" id="email" required>

<label for="password">Password *</label>
<input type="password" name="password" id="password" required>

<label for="parent_id">ID ya Mzazi</label>
<input type="number" id="parent_id" name="parent_id" placeholder="Andika ID ya mzazi">

<div id="parentName"></div>
<div id="displayChildID">ID ya mtoto itakuwa: <span id="childID">1</span></div>

<label for="photo">Picha</label>
<input type="file" name="photo" id="photo" accept="image/*">

<button type="submit" class="btn btn-success">Sajili</button>
</form>
</div>

<script>
$('#hasChildren').change(function(){
    $('#childrenFields').toggle(this.checked);
});

// AJAX: show parent name and next child ID
$('#parent_id').on('input', function(){
    let parent_id = $(this).val();
    if(parent_id===''){ $('#parentName').text(''); $('#childID').text('1'); return; }
    $.post('get_parent_info.php', {parent_id: parent_id}, function(data){
        try{
            let obj = JSON.parse(data);
            if(obj.error){ $('#parentName').text(obj.error); $('#childID').text('Error'); }
            else{ $('#parentName').text('Mzazi: '+obj.name); $('#childID').text(obj.next_child_id); }
        }catch(e){ $('#parentName').text('Tatizo la server'); $('#childID').text('Error'); }
    });
});
</script>
</body>
</html>
