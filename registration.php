<?php
include 'config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$message = '';

// Function to generate unique username
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

// Read CSVs from ZIP and build locations array
function loadCSVfromZip($zipPath) {
    $locations = [];
    $zip = new ZipArchive();
    if ($zip->open($zipPath) === TRUE) {
        $region = $district = $ward = $village = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (strpos($filename, 'region.csv') !== false) {
                $region = getCSVContentsFromZip($zip, $filename);
            } elseif (strpos($filename, 'district.csv') !== false) {
                $district = getCSVContentsFromZip($zip, $filename);
            } elseif (strpos($filename, 'ward.csv') !== false) {
                $ward = getCSVContentsFromZip($zip, $filename);
            } elseif (strpos($filename, 'village.csv') !== false) {
                $village = getCSVContentsFromZip($zip, $filename);
            }
        }
        $zip->close();

        // Build locations hierarchical array
        foreach ($region as $regionRow) {
            $region = trim($regionRow['Region'] ?? $regionRow[0]);
            if ($region) $locations[$region] = [];
        }
        foreach ($district as $distRow) {
            $region = trim($distRow['Region'] ?? $distRow);
            $district = trim($distRow['District'] ?? $distRow);
            if ($region && $district) {
                if (!isset($locations[$region])) $locations[$region] = [];
                $locations[$region][$district] = [];
            }
        }
        foreach ($ward as $wardRow) {
            $region = trim($wardRow['Region'] ?? $wardRow);
            $district = trim($wardRow['District'] ?? $wardRow);
            $ward = trim($wardRow['Ward'] ?? $wardRow);
            if ($region && $district && $ward) {
                if (!isset($locations[$region])) $locations[$region] = [];
                if (!isset($locations[$region][$district])) $locations[$region][$district] = [];
                $locations[$region][$district][$ward] = [];
            }
        }
        foreach ($village as $villageRow) {
            $region = trim($villageRow['Region'] ?? $villageRow[0]);
            $district = trim($villageRow['District'] ?? $villageRow);
            $ward = trim($villageRow['Ward'] ?? $villageRow);
            $village = trim($villageRow['Village'] ?? $villageRow);
            if ($region && $district && $ward && $village) {
                if (!isset($locations[$region])) $locations[$region] = [];
                if (!isset($locations[$region][$district])) $locations[$region][$district] = [];
                if (!isset($locations[$region][$district][$ward])) $locations[$region][$district][$ward] = [];
                $locations[$region][$district][$ward][] = $village;
            }
        }
    } else {
        throw new Exception("Failed to open ZIP archive");
    }
    return $locations;
}

function getCSVContentsFromZip($zip, $filename) {
    $fp = $zip->getStream($filename);
    if (!$fp) return [];

    $csv = [];
    $headers = [];
    $rowIndex = 0;

    while (($row = fgetcsv($fp)) !== false) {
        if ($rowIndex === 0) {
            $headers = $row;
        } else {
            if(count($headers) == count($row)){
                $csv[] = array_combine($headers, $row);
            }
        }
        $rowIndex++;
    }
    fclose($fp);
    return $csv;
}

try {
    $locations = loadCSVfromZip(__DIR__ . '/tanzania_locations.zip');
} catch (Exception $e) {
    $message = "Tatizo la kupakia data za maeneo: " . $e->getMessage();
}

$district = [];
$ward = [];
$village = [];

if (isset($_POST['region']) && isset($locations[$_POST['region']])) {
    $district = $locations[$_POST['region']];
}

if (isset($_POST['districtSelect']) && isset($districts[$_POST['districtSelect']])) {
    $ward = $district[$_POST['districtSelect']];
}

if (isset($_POST['wardSelect']) && isset($wards[$_POST['wardSelect']])) {
    $village = $wards[$_POST['wardSelect']];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name      = trim($_POST['first_name']);
    $middle_name     = trim($_POST['middle_name'] ?? '');
    $last_name       = trim($_POST['last_name']);
    $dob             = $_POST['dob'];
    $gender          = $_POST['gender'];
    $marital_status  = $_POST['marital_status'];
    $has_children    = isset($_POST['has_children']) ? 1 : 0;
    $children_male   = (int)($_POST['children_male'] ?? 0);
    $children_female = (int)($_POST['children_female'] ?? 0);
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

    try {
        $username = generateUniqueUsername($conn, $first_name, $middle_name, $last_name);
    } catch (Exception $e) {
        $message = "Imeshindikana kuunda jina la kipekee la mtumiaji. Jaribu tena.";
    }

    $photo = '';
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo);
    }

    if (!$message) {
        $sql = "INSERT INTO family_tree (
            id, username, first_name, middle_name, last_name, dob, gender, marital_status,
            has_children, children_male, children_female, country, region, district,
            ward, village, city, phone, email, password, photo, parent_id
        ) VALUES (
            $1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22
        )";
        $params = [
            $new_id, $username, $first_name, $middle_name, $last_name, $dob, $gender,
            $marital_status, $has_children, $children_male, $children_female,
            $country, $region, $district, $ward, $village, $city,
            $phone, $email, $password, $photo, $parent_id
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
    <label for="first_name">Jina la Kwanza *</label>
    <input type="text" id="first_name" name="first_name" required>
    <label for="middle_name">Jina la Kati</label>
    <input type="text" id="middle_name" name="middle_name">
    <label for="last_name">Jina la Mwisho *</label>
    <input type="text" id="last_name" name="last_name" required>
    <label for="dob">Tarehe ya Kuzaliwa *</label>
    <input type="date" id="dob" name="dob" required>
    <label for="gender">Jinsia *</label>
    <select id="gender" name="gender" required>
      <option value="" disabled selected>--Chagua--</option>
      <option value="male">Mwanaume</option>
      <option value="female">Mwanamke</option>
    </select>
    <label for="marital_status">Hali ya Ndoa *</label>
    <select id="marital_status" name="marital_status" required>
      <option value="" disabled selected>--Chagua--</option>
      <option value="single">Sijaoa/Sijaolewa</option>
      <option value="married">Nimeoa/Nimeolewa</option>
    </select>
    <div class="form-check">
      <input type="checkbox" id="has_children" name="has_children">
      <label for="has_children" style="margin:0;">Una watoto?</label>
    </div>
    <div id="childrenFields" style="display:none;" class="form-inline">
      <label for="children_male" style="margin:0;">Idadi ya Watoto wa Kiume</label>
      <input type="number" id="children_male" name="children_male" min="0" value="0" style="width:60px;">
      <label for="children_female" style="margin:0;">Idadi ya Watoto wa Kike</label>
      <input type="number" id="children_female" name="children_female" min="0" value="0" style="width:60px;">
    </div>
    <label for="country">Nchi *</label>
    <select id="country" name="country" required>
      <option value="Tanzania" selected>Tanzania</option>
      <option value="Other">Nyingine</option>
    </select>
    <label for="regionSelect">Mkoa *</label>
    <select id="regionSelect" name="region" required>
      <option value="">--Chagua Mkoa--</option>
      <?php foreach ($locations ?? [] as $region => $district): ?>
        <option value="<?= htmlspecialchars($region) ?>"><?= htmlspecialchars($region) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="districtSelect">Wilaya *</label>
    <select id="districtSelect" name="districtSelect" required disabled>
      <option value="">--Chagua Wilaya--</option>
    </select>
    <label for="wardSelect">Kata *</label>
    <select id="wardSelect" name="wardSelect" required disabled>
      <option value="">--Chagua Kata--</option>
    </select>
    <label for="villageSelect">Kijiji/Mtaa *</label>
    <select id="villageSelect" name="villageSelect" required disabled>
      <option value="">--Chagua Kijiji/Mtaa--</option>
    </select>
    <label for="phone">Namba ya Simu *</label>
    <input type="text" id="phone" name="phone" required>
    <label for="email">Email *</label>
    <input type="email" id="email" name="email" required>
    <label for="password">Password *</label>
    <input type="password" id="password" name="password" required>
    <label for="parent_id">ID ya Mzazi (Parent ID)</label>
    <input type="number" id="parent_id" name="parent_id">
    <div id="parentName" style="margin-bottom:10px;"></div>
    <div id="displayChildID" style="margin-bottom:20px;">ID ya mtoto itakuwa: <span id="childID">1</span></div>
    <label for="photo">Picha</label>
    <input type="file" id="photo" name="photo" accept="image/*">
    <button type="submit" class="btn-submit">Sajili</button>
  </form>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $("#has_children").change(function () {
        $("#childrenFields").toggle(this.checked);
    });

    let locations = <?= json_encode($locations) ?>;

    function populateDistricts() {
        let region = $("#regionSelect").val();
        let dis = $("#districtSelect");
        dis.html('<option value="">--Chagua Wilaya--</option>');
        if (region && locations[region]) {
            Object.keys(locations[region]).forEach(function(key) {
                dis.append($('<option></option>').attr("value", key).text(key));
            });
            dis.prop('disabled', false);
        } else {
            dis.prop('disabled', true);
        }
        populateWards();
    }

    function populateWards() {
        let region = $("#regionSelect").val();
        let district = $("#districtSelect").val();
        let ward = $("#wardSelect");
        ward.html('<option value="">--Chagua Kata--</option>');
        if (region && district && locations[region] && locations[region][district]) {
            Object.keys(locations[region][district]).forEach(function(key) {
                ward.append($('<option></option>').attr("value", key).text(key));
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
        let ward = $("#wardSelect").val();
        let vil = $("#villageSelect");
        vil.html('<option value="">--Chagua Kijiji/Mtaa--</option>');
        if (region && district && ward && locations[region] && locations[region][district] && locations[region][district][ward]) {
            locations[region][district][ward].forEach(function(village) {
                vil.append($('<option></option>').attr("value", village).text(village));
            });
            vil.prop('disabled', false);
        } else {
            vil.prop('disabled', true);
        }
    }

    $("#regionSelect").change(populateDistricts);
    $("#districtSelect").change(populateWards);
    $("#wardSelect").change(populateVillages);

    // AJAX parent info loader
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
