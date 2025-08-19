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
    $dob             = $_POST['dob'] ?? null;
    $gender          = $_POST['gender'] ?? null;
    $marital_status  = $_POST['marital_status'] ?? null;
    $has_children    = isset($_POST['has_children']) ? 1 : 0;
    $children_male   = intval($_POST['children_male'] ?? 0);
    $children_female = intval($_POST['children_female'] ?? 0);
    $country         = $_POST['country'] ?? null;
    $region          = $_POST['region'] ?? null;
    $district        = $_POST['districtSelect'] ?? null;
    $ward            = $_POST['wardSelect'] ?? null;
    $village         = $_POST['villageSelect'] ?? null;
    $city            = $_POST['city'] ?? '';
    $phone           = $_POST['phone'] ?? '';
    $email           = $_POST['email'] ?? '';
    $plain_password  = $_POST['password'] ?? '';
    $password        = password_hash($plain_password, PASSWORD_DEFAULT);
    $parent_id       = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    if ($parent_id) {
        $res_max = pg_query_params($conn, "SELECT MAX(id) AS maxid FROM family_tree WHERE parent_id = $1", [$parent_id]);
        if ($res_max && pg_num_rows($res_max) > 0) {
            $row_max = pg_fetch_assoc($res_max);
            $max_child_id = $row_max['maxid'];
            if ($max_child_id) {
                $new_id = (int)$max_child_id + 1;
            } else {
                $new_id = $parent_id * 1000 + 1;
            }
        } else {
            $new_id = $parent_id * 1000 + 1;
        }
    } else {
        $res_id = pg_query($conn, "SELECT MAX(id) as maxid FROM family_tree");
        if ($res_id) {
            $row_id = pg_fetch_assoc($res_id);
            $new_id = $row_id['maxid'] ? ((int)$row_id['maxid'] + 1) : 1;
        } else {
            $new_id = 1;
        }
    }

    if (empty($message)) {
        $generated_username = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst(strtolower($first_name))) . $new_id;

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
            )
            ON CONFLICT (id) DO NOTHING";
            $params = [
                $new_id, $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
                $has_children, $children_male, $children_female, $country, $region, $district,
                $ward, $village, $city, $phone, $email, $password, $photo, $parent_id, $generated_username
            ];
            $result = pg_query_params($conn, $sql, $params);
            if ($result) {
                if (pg_affected_rows($result) == 0) {
                    $message = "<div class='alert alert-warning'>Rekodi tayari ipo na haikuingizwa mara nyingine.</div>";
                } else {
                    $showSuccess = true;
                }
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
        <div class="mb-3">
            <label for="first_name">Jina la Kwanza *</label>
            <input type="text" id="first_name" name="first_name" required class="form-control" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="middle_name">Jina la Kati</label>
            <input type="text" id="middle_name" name="middle_name" class="form-control" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="last_name">Jina la Mwisho</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="dob">Tarehe ya Kuzaliwa</label>
            <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="gender">Jinsia</label>
            <select id="gender" name="gender" class="form-select">
                <option value="" disabled selected>--Chagua--</option>
                <option value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Mwanaume</option>
                <option value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Mwanamke</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="marital_status">Hali ya Ndoa</label>
            <select id="marital_status" name="marital_status" class="form-select">
                <option value="" disabled selected>--Chagua--</option>
                <option value="single" <?= (($_POST['marital_status'] ?? '') === 'single') ? 'selected' : '' ?>>Sijaoa/Sijaolewa</option>
                <option value="married" <?= (($_POST['marital_status'] ?? '') === 'married') ? 'selected' : '' ?>>Nimeoa/Nimeolewa</option>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" id="hasChildren" name="has_children" class="form-check-input" <?= isset($_POST['has_children']) ? 'checked' : '' ?>>
            <label for="hasChildren" class="form-check-label">Una Watoto?</label>
        </div>
        <div id="childrenFields" style="display: <?= isset($_POST['has_children']) ? 'block' : 'none' ?>;">
            <div class="mb-3">
                <label for="children_male">Idadi ya Watoto wa Kiume</label>
                <input type="number" id="children_male" name="children_male" min="0" class="form-control" value="<?= htmlspecialchars($_POST['children_male'] ?? '0') ?>">
            </div>
            <div class="mb-3">
                <label for="children_female">Idadi ya Watoto wa Kike</label>
                <input type="number" id="children_female" name="children_female" min="0" class="form-control" value="<?= htmlspecialchars($_POST['children_female'] ?? '0') ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="country">Nchi</label>
            <select id="country" name="country" class="form-select" required>
                <option value="Tanzania" <?= (($_POST['country'] ?? '') === 'Tanzania') ? 'selected' : '' ?>>Tanzania</option>
                <option value="Other" <?= (($_POST['country'] ?? '') === 'Other') ? 'selected' : '' ?>>Nyingine</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="regionSelect">Mkoa</label>
            <select id="regionSelect" name="region" class="form-select"></select>
        </div>
        <div class="mb-3">
            <label for="districtSelect">Wilaya</label>
            <select id="districtSelect" name="districtSelect" class="form-select"></select>
        </div>
        <div class="mb-3">
            <label for="wardSelect">Kata</label>
            <select id="wardSelect" name="wardSelect" class="form-select"></select>
        </div>
        <div class="mb-3">
            <label for="villageSelect">Kijiji/Mtaa</label>
            <select id="villageSelect" name="villageSelect" class="form-select"></select>
        </div>
        <div class="mb-3">
            <label for="city">Jiji</label>
            <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="phone">Namba ya Simu</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required class="form-control" value="">
        </div>
        <div class="mb-3">
            <label for="parent_id">ID ya Mzazi (Parent ID)</label>
            <input type="number" id="parent_id" name="parent_id" class="form-control" value="<?= htmlspecialchars($_POST['parent_id'] ?? '') ?>">
            <div id="parentName" class="mt-1 mb-2"></div>
        </div>
        <div class="mb-3">
            <label for="photo">Picha</label>
            <input type="file" id="photo" name="photo" accept="image/*" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Sajili</button>
    </form>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $("#hasChildren").change(function () {
        $("#childrenFields").toggle(this.checked);
    });
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
    $("#parent_id").on("input", function () {
        let pid = $(this).val();
        if (pid === '') {
            $("#parentName").text('');
            return;
        }
        $.post('get_parent_info.php', { parent_id: pid }, function (data) {
            try {
                let obj = JSON.parse(data);
                if (obj.error) {
                    $("#parentName").text(obj.error);
                } else {
                    $("#parentName").text('Mzazi: ' + obj.name);
                }
            } catch (e) {
                $("#parentName").text('Tatizo la server');
            }
        });
    });
});
</script>
</body>
</html>
