<?php
include 'config.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name      = $_POST['first_name'];
    $middle_name     = $_POST['middle_name'] ?? '';
    $last_name       = $_POST['last_name'];
    $dob             = $_POST['dob'];
    $gender          = $_POST['gender'];
    $marital_status  = $_POST['marital_status'];
    $has_children    = isset($_POST['has_children']) ? 1 : 0;
    $children_male   = $_POST['children_male'] ?? 0;
    $children_female = $_POST['children_female'] ?? 0;
    $country         = $_POST['country'];
    $region          = $_POST['region'];
    $district        = $_POST['districtSelect'];
    $ward            = $_POST['wardSelect'];
    $village         = $_POST['villageSelect'];
    $city            = $_POST['city'] ?? '';
    $phone           = $_POST['phone'];
    $email           = $_POST['email'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $parent_id       = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    // Calculate new child id (same logic as before)...
    if ($parent_id) {
    // Pata mtoto wa mwisho wa mzazi huyu
    $res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id = $1 ORDER BY id DESC LIMIT 1", [$parent_id]);
    if ($res_max && pg_num_rows($res_max) > 0) {
        $row_max = pg_fetch_assoc($res_max);
        $last_child_id = (int)$row_max['id'];
        $parent_digits = (string)$parent_id;
        // Tenga sehemu ya mtoto kutoka kwenye ID ya mwisho
        $last_digits = substr($last_child_id, strlen($parent_digits));
        $next_digit = (int)$last_digits + 1;
        if ($next_digit > 999) {
            echo "<div class='alert alert-danger text-center'>Mzazi tayari ana watoto 999</div>";
            exit;
        }
        // Jenga ID mpya kwa kunakili mzazi na kuongeza namba mpya ya mtoto
        $new_id = (int)($parent_digits . str_pad($next_digit, strlen($last_digits), '0', STR_PAD_LEFT));
    } else {
        // Haja bado mtoto yeyote, anza na 1
        $new_id = (int)($parent_id . '1');
    }
} else {
    // Hauna mzazi, mtu huyu ana ID ya 1
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
        $message = "Usajili umefanikiwa! <a href='family_tree.php'>Angalia ukoo</a>";
    } else {
        $message = "Tatizo limejitokeza: " . pg_last_error($conn);
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
    <div class="alert"><?= $message ?></div>
  <?php endif; ?>
  <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>
  <form method="post" enctype="multipart/form-data" id="regForm" novalidate>

    <div class="step active">
      <label>Jina la Kwanza *</label>
      <input type="text" name="first_name" required>
      <label>Jina la Kati</label>
      <input type="text" name="middle_name">
      <label>Jina la Mwisho *</label>
      <input type="text" name="last_name" required>
    </div>

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
        <label for="hasChildren" class="form-check-label">Una Watoto?</label>
      </div>
      <div id="childrenFields" style="display:none;">
        <label>Idadi ya Watoto wa Kiume</label>
        <input type="number" name="children_male" min="0" value="0">
        <label>Idadi ya Watoto wa Kike</label>
        <input type="number" name="children_female" min="0" value="0">
      </div>
    </div>

    <div class="step">
      <label>Nchi *</label>
      <select name="country" id="countrySelect" required>
        <option value="Tanzania" selected>Tanzania</option>
        <option value="Other">Nyingine</option>
      </select>
      <label>Mkoa *</label>
      <select name="region" id="regionSelect" required></select>
      <label>Wilaya *</label>
      <select name="districtSelect" id="districtSelect" required></select>
      <label>Kata *</label>
      <select name="wardSelect" id="wardSelect" required></select>
      <label>Kijiji/Mtaa *</label>
      <select name="villageSelect" id="villageSelect" required></select>
    </div>

    <div class="step">
      <label>Namba ya Simu *</label>
      <input type="text" name="phone" required>
      <label>Email *</label>
      <input type="email" name="email" required>
      <label>Password *</label>
      <input type="password" name="password" required>
    </div>

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
    <button type="submit" class="btn-submit" style="display:none">Sajili</button>
  </form>
</div>

<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Multi-step form logic
$(function () {
    let currentStep = 0;
    const steps = $(".step");
    const nextBtn = $("#nextBtn");
    const prevBtn = $("#prevBtn");
    const submitBtn = $(".btn-submit");
    const progressBar = $("#progressBar");

    function showStep(n) {
        steps.removeClass("active").eq(n).addClass("active");
        prevBtn.prop("disabled", n === 0);
        if (n === steps.length -1) {
            nextBtn.hide();
            submitBtn.show();
        } else {
            nextBtn.show();
            submitBtn.hide();
        }
        if (progressBar.length) {
            progressBar.css("width", ((n + 1) / steps.length) * 100 + "%");
        }
    }

    function validateStep() {
        let valid = true;
        steps.eq(currentStep).find("input,select").each(function () {
            if ($(this).prop("required") && !$(this).val()) {
                alert("Tafadhali jaza " + $(this).prev("label").text());
                valid = false;
                return false;
            }
        });
        return valid;
    }

    nextBtn.click(function () {
        if (!validateStep()) return;
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.click(function () {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    showStep(currentStep);

    // Show/hide children fields on checkbox toggle
    $("#hasChildren").change(function() {
        $("#childrenFields").toggle(this.checked);
    });

    // Locations cascading dropdowns data loading from JSON
    let locData = {};
    $.getJSON('tanzania_locations.json', function(data) {
        locData = data;
        populateRegions();
    });

    function populateRegions() {
        let reg = $("#regionSelect");
        reg.html('<option value="">--Chagua Mkoa--</option>');
        $.each(locData, function(key) {
            reg.append($('<option></option>').attr("value", key).text(key));
        });
        populateDistricts();
    }
    function populateDistricts() {
        let reg = $("#regionSelect").val();
        let dis = $("#districtSelect");
        dis.html('<option value="">--Chagua Wilaya--</option>');
        if (locData[reg]) {
            $.each(locData[reg], function(key) {
                dis.append($('<option></option>').attr("value", key).text(key));
            });
        }
        populateWards();
    }
    function populateWards() {
        let reg = $("#regionSelect").val();
        let dis = $("#districtSelect").val();
        let ward = $("#wardSelect");
        ward.html('<option value="">--Chagua Kata--</option>');
        if (locData[reg] && locData[reg][dis]) {
            $.each(locData[reg][dis], function(key) {
                ward.append($('<option></option>').attr("value", key).text(key));
            });
        }
        populateVillages();
    }
    function populateVillages() {
        let reg = $("#regionSelect").val();
        let dis = $("#districtSelect").val();
        let ward = $("#wardSelect").val();
        let vil = $("#villageSelect");
        vil.html('<option value="">--Chagua Kijiji/Mtaa--</option>');
        if (locData[reg] && locData[reg][dis] && locData[reg][dis][ward]) {
            $.each(locData[reg][dis][ward], function(i, village) {
                vil.append($('<option></option>').attr("value", village).text(village));
            });
        }
    }

    $("#regionSelect").change(populateDistricts);
    $("#districtSelect").change(populateWards);
    $("#wardSelect").change(populateVillages);

    // AJAX to get parent info
    $("#parent_id").on("input", function() {
        let pid = $(this).val();
        if (pid === '') {
            $("#parentName").text('');
            $("#childID").text('1');
            return;
        }
        $.post('get_parent_info.php', { parent_id: pid }, function(data) {
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
});
</script>
<script src="scripts.js"></script>
</body>
</html>
