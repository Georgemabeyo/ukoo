<?php
include 'config.php';
session_start();

$message = '';
$showSuccess = false;
$generated_username = '';
$plain_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name      = trim($_POST['first_name']);
    $middle_name     = trim($_POST['middle_name'] ?? '');
    $last_name       = trim($_POST['last_name']);
    $dob             = $_POST['dob'];
    $gender          = $_POST['gender'];
    $marital_status  = $_POST['marital_status'];
    $has_children    = isset($_POST['has_children']) ? 1 : 0;
    $children_male   = intval($_POST['children_male'] ?? 0);
    $children_female = intval($_POST['children_female'] ?? 0);
    $country         = $_POST['country'];
    $region          = $_POST['region'];
    $district        = $_POST['districtSelect'];
    $ward            = $_POST['wardSelect'];
    $village         = $_POST['villageSelect'];
    $city            = $_POST['city'] ?? '';
    $phone           = $_POST['phone'];
    $email           = $_POST['email'];
    $plain_password  = $_POST['password']; // This will be shown to user after success
    $password        = password_hash($plain_password, PASSWORD_DEFAULT);
    $parent_id       = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    // Generate new ID based on parent_id
    if ($parent_id) {
        $res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id = $1 ORDER BY id DESC LIMIT 1", [$parent_id]);
        if ($res_max && pg_num_rows($res_max) > 0) {
            $row_max = pg_fetch_assoc($res_max);
            $last_child_id = (int)$row_max['id'];
            $parent_digits = (string)$parent_id;
            $last_digits = substr($last_child_id, strlen($parent_digits));
            $next_digit = (int)$last_digits + 1;
            if ($next_digit > 999) {
                $message = "<div class='alert alert-danger text-center'>Mzazi tayari ana watoto 999</div>";
            } else {
                $new_id = (int)($parent_digits . str_pad($next_digit, strlen($last_digits), '0', STR_PAD_LEFT));
            }
        } else {
            $new_id = (int)($parent_id . '1');
        }
    } else {
        // Root ID
        $res_id = pg_query($conn, "SELECT MAX(id) as maxid FROM family_tree");
        if ($res_id) {
            $row_id = pg_fetch_assoc($res_id);
            $new_id = $row_id['maxid'] ? ((int)$row_id['maxid'] + 1) : 1;
        } else {
            $new_id = 1;
        }
    }

    if (empty($message)) {

        // Generate username = firstName + new_id
        $generated_username = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst(strtolower($first_name))) . $new_id;

        // Photo upload
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $target_dir = __DIR__ . "/uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $photo = time() . "_" . basename($_FILES["photo"]["name"]);
            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo)) {
                $message = "<div class='alert alert-danger'>Imeshindikana kupakia picha.</div>";
            }
        }

        if (empty($message)) {
            $sql = "INSERT INTO family_tree (
                id, first_name, middle_name, last_name, dob, gender, marital_status,
                has_children, children_male, children_female, country, region, district,
                ward, village, city, phone, email, password, photo, parent_id, username
            ) VALUES (
                $1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22
            )";
            $params = [
                $new_id, $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
                $has_children, $children_male, $children_female, $country, $region, $district,
                $ward, $village, $city, $phone, $email, $password, $photo, $parent_id, $generated_username
            ];
            $result = pg_query_params($conn, $sql, $params);

            if ($result) {
                // On success, show thank you page below instead of redirect.
                $showSuccess = true;
            } else {
                $message = "<div class='alert alert-danger'>Tatizo limejitokeza: " . pg_last_error($conn) . "</div>";
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<?php include 'header.php'; ?>

<div class="container mt-5">
    <?php if($showSuccess): ?>
        <div class="alert alert-success text-center">
            <h2>Asante kwa kujisajili, <?= htmlspecialchars($first_name) ?>!</h2>
            <p>Username yako ni: <strong><?= htmlspecialchars($generated_username) ?></strong></p>
            <p>Password yako ni: <strong><?= htmlspecialchars($plain_password) ?></strong></p>
            <a href="index.php" class="btn btn-primary mt-3">Rudi Nyumbani</a>
        </div>

    <?php else: ?>
    
    <?php if($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <h2>Usajili wa Ukoo wa Makomelelo</h2>
    <form method="post" enctype="multipart/form-data" id="regForm" novalidate>
        <!-- Step 1 -->
        <div class="step active">
            <label>Jina la Kwanza *</label>
            <input type="text" name="first_name" required class="form-control" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
            <label>Jina la Kati</label>
            <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
            <label>Jina la Mwisho *</label>
            <input type="text" name="last_name" required class="form-control" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
        <!-- Step 2 -->
        <div class="step">
            <label>Tarehe ya Kuzaliwa *</label>
            <input type="date" name="dob" required class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
            <label>Jinsia *</label>
            <select name="gender" required class="form-select">
                <option value="" disabled <?= empty($_POST['gender']) ? 'selected' : '' ?>>--Chagua--</option>
                <option value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Mwanaume</option>
                <option value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Mwanamke</option>
            </select>
            <label>Hali ya Ndoa *</label>
            <select name="marital_status" required class="form-select">
                <option value="" disabled <?= empty($_POST['marital_status']) ? 'selected' : '' ?>>--Chagua--</option>
                <option value="single" <?= (($_POST['marital_status'] ?? '') === 'single') ? 'selected' : '' ?>>Sijaoa/Sijaolewa</option>
                <option value="married" <?= (($_POST['marital_status'] ?? '') === 'married') ? 'selected' : '' ?>>Nimeoa/Nimeolewa</option>
            </select>
            <div class="form-check">
                <input type="checkbox" name="has_children" id="hasChildren" class="form-check-input" <?= isset($_POST['has_children']) ? 'checked' : '' ?>>
                <label for="hasChildren" class="form-check-label">Una Watoto?</label>
            </div>
            <div id="childrenFields" style="display: <?= isset($_POST['has_children']) ? 'block' : 'none' ?>;">
                <label>Idadi ya Watoto wa Kiume</label>
                <input type="number" name="children_male" min="0" value="<?= htmlspecialchars($_POST['children_male'] ?? '0') ?>" class="form-control">
                <label>Idadi ya Watoto wa Kike</label>
                <input type="number" name="children_female" min="0" value="<?= htmlspecialchars($_POST['children_female'] ?? '0') ?>" class="form-control">
            </div>
        </div>
        <!-- Step 3 -->
        <div class="step">
            <label>Nchi *</label>
            <select name="country" id="countrySelect" required class="form-select">
                <option value="Tanzania" <?= (($_POST['country'] ?? '') === 'Tanzania') ? 'selected' : '' ?>>Tanzania</option>
                <option value="Other" <?= (($_POST['country'] ?? '') === 'Other') ? 'selected' : '' ?>>Nyingine</option>
            </select>
            <label>Mkoa *</label>
            <select name="region" id="regionSelect" required class="form-select"></select>
            <label>Wilaya *</label>
            <select name="districtSelect" id="districtSelect" required class="form-select"></select>
            <label>Kata *</label>
            <select name="wardSelect" id="wardSelect" required class="form-select"></select>
            <label>Kijiji/Mtaa *</label>
            <select name="villageSelect" id="villageSelect" required class="form-select"></select>
            <label>Namba ya Simu *</label>
            <input type="text" name="phone" required class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            <label>Email *</label>
            <input type="email" name="email" required class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Password *</label>
            <input type="password" name="password" required class="form-control">
            <label>ID ya Mzazi (Parent ID)</label>
            <input type="number" name="parent_id" id="parent_id" value="<?= htmlspecialchars($_POST['parent_id'] ?? '') ?>" class="form-control">
            <div id="parentName" class="mt-1 mb-2"></div>
            <div id="displayChildID" class="mb-3">ID ya mtoto itakuwa: <span id="childID">1</span></div>
            <label>Picha</label>
            <input type="file" name="photo" accept="image/*" class="form-control">
        </div>
        <div class="btn-group mt-3">
            <button type="button" id="prevBtn" class="btn btn-secondary" disabled>&larr; Nyuma</button>
            <button type="button" id="nextBtn" class="btn btn-primary">Mbele &rarr;</button>
            <button type="submit" class="btn btn-success d-none">Sajili</button>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    // Multi-step form
    let currentStep = 0;
    const steps = $(".step");
    const nextBtn = $("#nextBtn");
    const prevBtn = $("#prevBtn");
    const submitBtn = $(".btn-success");

    function showStep(n) {
        steps.removeClass("active").eq(n).addClass("active");
        prevBtn.prop("disabled", n === 0);
        if (n === steps.length - 1) {
            nextBtn.hide();
            submitBtn.removeClass("d-none");
        } else {
            nextBtn.show();
            submitBtn.addClass("d-none");
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

    // Toggle children count input
    $("#hasChildren").change(function () {
        $("#childrenFields").toggle(this.checked);
    });

    // Load dropdown data from JSON
    let locData = {};
    $.getJSON('tanzania_mikoa.json', function (data) {
        locData = data;
        populateRegions();
    });
    function populateRegions() {
        let reg = $("#regionSelect");
        reg.html('<option value="">--Chagua Mkoa--</option>');
        $.each(locData, function (key) {
            reg.append($('<option></option>').attr("value", key).text(key));
        });
        populateDistricts();
    }
    function populateDistricts() {
        let reg = $("#regionSelect").val();
        let dis = $("#districtSelect");
        dis.html('<option value="">--Chagua Wilaya--</option>');
        if (locData[reg]) {
            $.each(locData[reg], function (key) {
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
            $.each(locData[reg][dis], function (key) {
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
            $.each(locData[reg][dis][ward], function (i, village) {
                vil.append($('<option></option>').attr("value", village).text(village));
            });
        }
    }
    $("#regionSelect").change(populateDistricts);
    $("#districtSelect").change(populateWards);
    $("#wardSelect").change(populateVillages);

    // AJAX fetch parent info and next child ID
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
});
</script>
</body>
</html>
