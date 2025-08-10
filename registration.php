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
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
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
    <meta charset="UTF-8">
    <title>Usajili - Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .step { display: none; }
        .step.active { display: block; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Form ya Usajili wa Ukoo wa Makomelelo</h2>
    <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">

        <!-- Step 1: Majina -->
        <div class="step active">
            <div class="mb-3">
                <label>Jina la Kwanza</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
        </div>

        <div class="step">
            <div class="mb-3">
                <label>Jina la Kati</label>
                <input type="text" name="middle_name" class="form-control">
            </div>
        </div>

        <div class="step">
            <div class="mb-3">
                <label>Jina la Mwisho</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
        </div>

        <!-- Step 2: Taarifa za Msingi -->
        <div class="step">
            <div class="mb-3">
                <label>Tarehe ya Kuzaliwa</label>
                <input type="date" name="dob" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Jinsia</label>
                <select name="gender" class="form-select" required>
                    <option value="">--Chagua--</option>
                    <option value="male">Mwanaume</option>
                    <option value="female">Mwanamke</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Hali ya Ndoa</label>
                <select name="marital_status" class="form-select" required>
                    <option value="">--Chagua--</option>
                    <option value="single">Hajaoa/Hajaolewa</option>
                    <option value="married">Kaoa/Ameolewa</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="has_children" class="form-check-input" id="hasChildren">
                <label class="form-check-label" for="hasChildren">Ana Watoto?</label>
            </div>
            <div id="childrenFields" style="display:none;">
                <div class="mb-3">
                    <label>Idadi ya Watoto wa Kiume</label>
                    <input type="number" name="children_male" value="0" min="0" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Idadi ya Watoto wa Kike</label>
                    <input type="number" name="children_female" value="0" min="0" class="form-control">
                </div>
            </div>
        </div>

        <!-- Step 3: Makazi -->
        <div class="step">
            <div class="mb-3">
                <label>Nchi</label>
                <select name="country" id="country" class="form-select" required>
                    <option value="Tanzania" selected>Tanzania</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Other">Nyingine</option>
                </select>
            </div>

            <div id="tz-fields">
                <div class="mb-3">
                    <label>Mkoa</label>
                    <select name="region" id="region" class="form-select" required>
                        <option value="">--Chagua Mkoa--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Wilaya</label>
                    <select name="district" id="district" class="form-select" required>
                        <option value="">--Chagua Wilaya--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kata</label>
                    <select name="ward" id="ward" class="form-select" required>
                        <option value="">--Chagua Kata--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kijiji/Mtaa</label>
                    <select name="village" id="village" class="form-select" required>
                        <option value="">--Chagua Kijiji/Mtaa--</option>
                    </select>
                </div>
            </div>

            <div id="other-country" style="display:none;">
                <div class="mb-3">
                    <label>Mji/Jiji</label>
                    <input type="text" name="city" class="form-control" placeholder="Andika Mji au Jiji">
                </div>
            </div>
        </div>

        <!-- Step 4: Mawasiliano & Mwingineyo -->
        <div class="step">
            <div class="mb-3">
                <label>Namba ya Simu</label>
                <input type="text" name="phone" class="form-control" required placeholder="Andika namba ya simu">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required placeholder="Andika barua pepe">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Weka nenosiri">
            </div>
            <div class="mb-3">
                <label>Mzazi (Parent ID)</label>
                <input type="number" name="parent_id" class="form-control" placeholder="Weka ID ya mzazi kama ipo">
            </div>
            <div class="mb-3">
                <label>Picha</label>
                <input type="file" name="photo" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sajili</button>
        </div>

    </form>
</div>

<script>
    // Data structure ya mikoa->wilaya->kata->kijiji/mtaa (Mfano tu)
    const data = {
        "Dar es Salaam": {
            "Ilala": {
                "Upanga East": ["Msasani", "Kivukoni"],
                "Mchikichini": ["Mtaa A", "Mtaa B"]
            },
            "Kinondoni": {
                "Kibamba": ["Kijiji A", "Kijiji B"],
                "Magomeni": ["Mtaa C", "Mtaa D"]
            }
        },
        "Dodoma": {
            "Dodoma Urban": {
                "Hombolo": ["Kijiji 1", "Kijiji 2"],
                "Tambukareli": ["Kijiji 3", "Kijiji 4"]
            },
            "Bahi": {
                "Bahi": ["Kijiji 5", "Kijiji 6"],
                "Chali": ["Kijiji 7", "Kijiji 8"]
            }
        }
    };

    const countrySelect = document.getElementById("country");
    const regionSelect = document.getElementById("region");
    const districtSelect = document.getElementById("district");
    const wardSelect = document.getElementById("ward");
    const villageSelect = document.getElementById("village");
    const tzFields = document.getElementById("tz-fields");
    const otherCountryFields = document.getElementById("other-country");

    // Jaza mikoa
    function fillRegions() {
        regionSelect.innerHTML = '<option value="">--Chagua Mkoa--</option>';
        for (let region in data) {
            regionSelect.innerHTML += `<option value="${region}">${region}</option>`;
        }
        districtSelect.innerHTML = '<option value="">--Chagua Wilaya--</option>';
        wardSelect.innerHTML = '<option value="">--Chagua Kata--</option>';
        villageSelect.innerHTML = '<option value="">--Chagua Kijiji/Mtaa--</option>';
    }

    // Jaza wilaya kulingana na mkoa
    regionSelect.addEventListener('change', () => {
        const selectedRegion = regionSelect.value;
        districtSelect.innerHTML = '<option value="">--Chagua Wilaya--</option>';
        wardSelect.innerHTML = '<option value="">--Chagua Kata--</option>';
        villageSelect.innerHTML = '<option value="">--Chagua Kijiji/Mtaa--</option>';
        if (selectedRegion && data[selectedRegion]) {
            for (let district in data[selectedRegion]) {
                districtSelect.innerHTML += `<option value="${district}">${district}</option>`;
            }
        }
    });

    // Jaza kata kulingana na wilaya
    districtSelect.addEventListener('change', () => {
        const selectedRegion = regionSelect.value;
        const selectedDistrict = districtSelect.value;
        wardSelect.innerHTML = '<option value="">--Chagua Kata--</option>';
        villageSelect.innerHTML = '<option value="">--Chagua Kijiji/Mtaa--</option>';
        if (selectedRegion && selectedDistrict && data[selectedRegion][selectedDistrict]) {
            for (let ward in data[selectedRegion][selectedDistrict]) {
                wardSelect.innerHTML += `<option value="${ward}">${ward}</option>`;
            }
        }
    });

    // Jaza kijiji/mtaa kulingana na kata
    wardSelect.addEventListener('change', () => {
        const selectedRegion = regionSelect.value;
        const selectedDistrict = districtSelect.value;
        const selectedWard = wardSelect.value;
        villageSelect.innerHTML = '<option value="">--Chagua Kijiji/Mtaa--</option>';
        if (selectedRegion && selectedDistrict && selectedWard && data[selectedRegion][selectedDistrict][selectedWard]) {
            for (let village of data[selectedRegion][selectedDistrict][selectedWard]) {
                villageSelect.innerHTML += `<option value="${village}">${village}</option>`;
            }
        }
    });

    // Show/hide makazi kulingana na nchi
    countrySelect.addEventListener("change", function() {
        if (this.value === "Tanzania") {
            tzFields.style.display = "block";
            otherCountryFields.style.display = "none";
        } else {
            tzFields.style.display = "none";
            otherCountryFields.style.display = "block";
        }
    });

    // Init
    window.addEventListener("DOMContentLoaded", () => {
        fillRegions();
        if (countrySelect.value === "Tanzania") {
            tzFields.style.display = "block";
            otherCountryFields.style.display = "none";
        } else {
            tzFields.style.display = "none";
            otherCountryFields.style.display = "block";
        }
    });

    // Form steps automatic advance logic
    const steps = document.querySelectorAll(".step");
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, i) => s.classList.toggle("active", i === step));
        window.scrollTo(0, 0);
    }

    function validateStep(step) {
        const inputs = steps[step].querySelectorAll("input[required], select[required]");
        for (let input of inputs) {
            if (!input.value) {
                return false;
            }
        }
        return true;
    }

    function tryNextStep() {
        if (validateStep(currentStep)) {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        }
    }

    steps.forEach((stepDiv, index) => {
        const inputs = stepDiv.querySelectorAll("input, select");
        inputs.forEach(input => {
            const eventType = input.tagName.toLowerCase() === "select" ? "change" : "input";
            input.addEventListener(eventType, () => {
                tryNextStep();
            });
        });
    });

    // Show/hide children fields
    document.addEventListener("DOMContentLoaded", () => {
        const hasChildrenCheckbox = document.getElementById("hasChildren");
        const childrenFields = document.getElementById("childrenFields");

        hasChildrenCheckbox.addEventListener("change", () => {
            childrenFields.style.display = hasChildrenCheckbox.checked ? "block" : "none";
        });
        childrenFields.style.display = hasChildrenCheckbox.checked ? "block" : "none";
    });
</script>
</body>
</html>
