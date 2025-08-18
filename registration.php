<?php
include 'config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$message = '';
$errors = [];

function generateUniqueUsername($conn, $first_name, $middle_name, $last_name) {
    $base_username = strtolower(preg_replace('/\s+/', '', $first_name . $middle_name . $last_name));
    $username = $base_username;
    $suffix = 0;
    while (true) {
        $sql_check = "SELECT COUNT(*) FROM family_tree WHERE username = $1";
        $res = pg_query_params($conn, $sql_check, [$username]);
        $count = 0;
        if ($res) {
            $row = pg_fetch_row($res);
            $count = (int)$row[0];
        }
        if ($count == 0) {
            return $username;
        }
        $suffix++;
        $username = $base_username . $suffix;
        if ($suffix > 9999) {
            throw new Exception("Unable to generate unique username.");
        }
    }
}

// Simulated loadCSVfromZip function omitted for brevity.
// Assume $locations is loaded and ready for dropdowns.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $has_children = isset($_POST['has_children']) ? 1 : 0;
    $children_male = (int)($_POST['children_male'] ?? 0);
    $children_female = (int)($_POST['children_female'] ?? 0);
    $country = $_POST['country'] ?? '';
    $region = $_POST['region'] ?? '';
    $district = $_POST['districtSelect'] ?? '';
    $ward = $_POST['wardSelect'] ?? '';
    $village = $_POST['villageSelect'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    // Validate required fields
    if ($first_name === '') $errors['first_name'] = 'Jina la Kwanza linahitajika.';
    if ($last_name === '') $errors['last_name'] = 'Jina la Mwisho linahitajika.';
    if ($dob === '') $errors['dob'] = 'Tarehe ya Kuzaliwa inahitajika.';
    if (!in_array($gender, ['male', 'female'])) $errors['gender'] = 'Chagua Jinsia.';
    if (!in_array($marital_status, ['single', 'married'])) $errors['marital_status'] = 'Chagua hali ya ndoa.';
    if ($phone === '') $errors['phone'] = 'Namba ya simu inahitajika.';
    if ($email === '') $errors['email'] = 'Email inahitajika.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email sio sahihi.';
    if (strlen($password) < 8) $errors['password'] = 'Password lazima iwe na angalau herufi 8.';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Password hazilingani.';

    // Validate location
    if ($region === '') $errors['region'] = 'Chagua Mkoa.';
    if ($district === '') $errors['districtSelect'] = 'Chagua Wilaya.';
    if ($ward === '') $errors['wardSelect'] = 'Chagua Kata.';
    if ($village === '') $errors['villageSelect'] = 'Chagua Kijiji/Mtaa.';

    // Proceed if no errors
    if (empty($errors)) {
        try {
            $username = generateUniqueUsername($conn, $first_name, $middle_name, $last_name);
        } catch (Exception $e) {
            $message = "Imeshindikana kuunda jina la mtumiaji.";
        }

        if (!$message) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $photo = '';
            if (!empty($_FILES['photo']['name'])) {
                $target_dir = __DIR__ . "/uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $photo = time() . "_" . basename($_FILES["photo"]["name"]);
                move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo);
            }
            // generate new id logic here same as before (omitted for brevity)...
if ($parent_id) {
    $res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id = $1 ORDER BY id DESC LIMIT 1", [$parent_id]);
    if ($res_max && pg_num_rows($res_max) > 0) {
        $row_max = pg_fetch_assoc($res_max);
        $last_child_id = (int)$row_max['id'];
        $parent_digits = (string)$parent_id;
        $last_digits = substr($last_child_id, strlen($parent_digits));
        $next_digit = (int)$last_digits + 1;
        if ($next_digit > 999) {
            echo "<div class='alert alert-danger text-center'>Mzazi tayari ana watoto 999</div>";
            exit;
        }
        $new_id = (int)($parent_digits . str_pad($next_digit, strlen($last_digits), '0', STR_PAD_LEFT));
    } else {
        $new_id = (int)($parent_id . '1');
    }
} else {
    $res_root_max = pg_query($conn, "SELECT MAX(id) as maxid FROM family_tree WHERE parent_id IS NULL");
    $row_root = pg_fetch_assoc($res_root_max);
    $max_root_id = $row_root ? (int)$row_root['maxid'] : 0;
    $new_id = $max_root_id + 1;
}

            // Insert into DB
            $sql = "INSERT INTO family_tree (
                id, username, first_name, middle_name, last_name, dob, gender, marital_status,
                has_children, children_male, children_female, country, region,
                district, ward, village, city, phone, email, password, photo, parent_id
            ) VALUES (
                $1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22
            )";

            $params = [
                $new_id, $username, $first_name, $middle_name, $last_name, $dob, $gender,
                $marital_status, $has_children, $children_male, $children_female,
                $country, $region, $district, $ward, $village, $city, $phone, $email,
                $password_hash, $photo, $parent_id
            ];

            $result = pg_query_params($conn, $sql, $params);
            if ($result) {
                header("Location: register_success.php?id=$new_id");
                exit();
            } else {
                $message = "Tatizo limejitokeza: " . pg_last_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Usajili Ukoo - Makomelelo</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<?php include 'header.php'; ?>
<div class="container">
  <h2>Usajili wa Ukoo wa Makomelelo</h2>
  <?php if($message): ?>
    <div class="alert"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <label>Jina la Kwanza *</label>
    <input type="text" name="first_name" value="<?=htmlspecialchars($_POST['first_name'] ?? '')?>">
    <div style="color:red;"><?= $errors['first_name'] ?? '' ?></div>

    <label>Jina la Kati</label>
    <input type="text" name="middle_name" value="<?=htmlspecialchars($_POST['middle_name'] ?? '')?>">

    <label>Jina la Mwisho *</label>
    <input type="text" name="last_name" value="<?=htmlspecialchars($_POST['last_name'] ?? '')?>">
    <div style="color:red;"><?= $errors['last_name'] ?? '' ?></div>

    <label>Tarehe ya Kuzaliwa *</label>
    <input type="date" name="dob" value="<?=htmlspecialchars($_POST['dob'] ?? '')?>">
    <div style="color:red;"><?= $errors['dob'] ?? '' ?></div>

    <label>Jinsia *</label>
    <select name="gender">
      <option value="">--Chagua--</option>
      <option value="male" <?= (($_POST['gender'] ?? '')==='male') ? 'selected' : ''?>>Mwanaume</option>
      <option value="female" <?= (($_POST['gender'] ?? '')==='female') ? 'selected' : ''?>>Mwanamke</option>
    </select>
    <div style="color:red;"><?= $errors['gender'] ?? '' ?></div>

    <label>Hali ya Ndoa *</label>
    <select name="marital_status">
      <option value="">--Chagua--</option>
      <option value="single" <?= (($_POST['marital_status'] ?? '')==='single') ? 'selected' : ''?>>Sijaoa/Sijaolewa</option>
      <option value="married" <?= (($_POST['marital_status'] ?? '')==='married') ? 'selected' : ''?>>Nimeoa/Nimeolewa</option>
    </select>
    <div style="color:red;"><?= $errors['marital_status'] ?? '' ?></div>

    <div>
      <input type="checkbox" name="has_children" <?= isset($_POST['has_children']) ? 'checked' : '' ?>>
      <label>Una watoto?</label>
    </div>

    <div>
      <label>Idadi ya Watoto wa Kiume</label>
      <input type="number" name="children_male" min="0" value="<?= (int)($_POST['children_male'] ?? 0) ?>">
      <label>Idadi ya Watoto wa Kike</label>
      <input type="number" name="children_female" min="0" value="<?= (int)($_POST['children_female'] ?? 0) ?>">
    </div>

    <label>Nchi *</label>
    <select name="country">
      <option value="Tanzania" <?= (($_POST['country'] ?? '')==='Tanzania') ? 'selected': ''?>>Tanzania</option>
      <option value="Other" <?= (($_POST['country'] ?? '')==='Other') ? 'selected': ''?>>Nyingine</option>
    </select>

    <label>Mkoa *</label>
    <select id="regionSelect" name="region" required>
      <option value="">--Chagua Mkoa--</option>
      <?php foreach ($locations ?? [] as $region => $districts): ?>
        <option value="<?= htmlspecialchars($region) ?>" <?= (($_POST['region'] ?? '') === $region) ? 'selected' : '' ?>><?= htmlspecialchars($region) ?></option>
      <?php endforeach; ?>
    </select>
    <div style="color:red;"><?= $errors['region'] ?? '' ?></div>

    <label>Wilaya *</label>
    <select id="districtSelect" name="districtSelect" required disabled>
      <option value="">--Chagua Wilaya--</option>
    </select>
    <div style="color:red;"><?= $errors['districtSelect'] ?? '' ?></div>

    <label>Kata *</label>
    <select id="wardSelect" name="wardSelect" required disabled>
      <option value="">--Chagua Kata--</option>
    </select>
    <div style="color:red;"><?= $errors['wardSelect'] ?? '' ?></div>

    <label>Kijiji/Mtaa *</label>
    <select id="villageSelect" name="villageSelect" required disabled>
      <option value="">--Chagua Kijiji/Mtaa--</option>
    </select>
    <div style="color:red;"><?= $errors['villageSelect'] ?? '' ?></div>

    <label>Namba ya Simu *</label>
    <input type="text" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
    <div style="color:red;"><?= $errors['phone'] ?? '' ?></div>

    <label>Email *</label>
    <input type="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
    <div style="color:red;"><?= $errors['email'] ?? '' ?></div>

    <label>Password *</label>
    <input type="password" name="password" required>
    <div style="color:red;"><?= $errors['password'] ?? '' ?></div>

    <label>Thibitisha Password *</label>
    <input type="password" name="confirm_password" required>
    <div style="color:red;"><?= $errors['confirm_password'] ?? '' ?></div>

    <label>ID ya Mzazi (Parent ID)</label>
    <input type="number" name="parent_id" value="<?=htmlspecialchars($_POST['parent_id'] ?? '')?>">

    <label>Picha</label>
    <input type="file" name="photo" accept="image/*">

    <button type="submit">Sajili</button>
  </form>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Handle dynamic dropdowns as before...

let locations = <?= json_encode($locations) ?>;

$(function(){
    function populateDistricts() {
        let region = $("#regionSelect").val();
        let dis = $("#districtSelect");
        dis.html('<option value="">--Chagua Wilaya--</option>');
        if(region && locations[region]){
            Object.keys(locations[region]).forEach(function(key){
                dis.append('<option value="'+key+'">'+key+'</option>');
            });
            dis.prop('disabled', false);
        }else{
            dis.prop('disabled', true);
        }
        populateWards();
    }
    function populateWards() {
        let region = $("#regionSelect").val();
        let district = $("#districtSelect").val();
        let ward = $("#wardSelect");
        ward.html('<option value="">--Chagua Kata--</option>');
        if(region && district && locations[region] && locations[region][district]){
            Object.keys(locations[region][district]).forEach(function(key){
                ward.append('<option value="'+key+'">'+key+'</option>');
            });
            ward.prop('disabled', false);
        } else {
            ward.prop('disabled', true);
        }
        populateVillages();
    }
    function populateVillages() {
        let region = $("#regionSelect").val();
        let district = $("#districtSelect").val();
        let wardVal = $("#wardSelect").val();
        let vil = $("#villageSelect");
        vil.html('<option value="">--Chagua Kijiji/Mtaa--</option>');
        if(region && district && wardVal && locations[region] && locations[region][district] && locations[region][district][wardVal]){
            locations[region][district][wardVal].forEach(function(village){
                vil.append('<option value="'+village+'">'+village+'</option>');
            });
            vil.prop('disabled', false);
        } else {
            vil.prop('disabled', true);
        }
    }

    $("#regionSelect").change(populateDistricts);
    $("#districtSelect").change(populateWards);
    $("#wardSelect").change(populateVillages);

    // Trigger populates if POST data exists (optional)
    let initRegion = "<?= htmlspecialchars($_POST['region'] ?? '') ?>";
    if(initRegion){
        $("#regionSelect").val(initRegion).change();
        // delayed triggers for districts, wards to select previously chosen values can be added
    }
});
</script>

</body>
</html>
