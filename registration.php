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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Usajili - Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .step { display: none; }
        .step.active { display: block; }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        input.form-control, select.form-select {
            font-size: 1.1rem;
            padding: 0.5rem 0.75rem;
        }
        button[type="submit"] {
            width: 100%;
            font-size: 1.2rem;
            padding: 0.6rem;
        }
        .mb-3 label {
            font-weight: 600;
            font-size: 1.05rem;
        }
        #childrenFields {
            margin-left: 20px;
            border-left: 2px solid #dee2e6;
            padding-left: 15px;
        }
        /* Nyumbani button styling */
        .back-home-btn {
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Form ya Usajili wa Ukoo wa Makomelelo</h2>
    
    <!-- Button ya kurudi nyumbani -->
    <a href="index.php" class="btn btn-outline-primary back-home-btn">&larr; Nyumbani</a>
    
    <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">

        <!-- Step 1: Majina -->
        <div class="step active">
            <div class="mb-3">
                <label for="first_name">Jina la Kwanza</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required autocomplete="given-name" />
            </div>
            <div class="mb-3">
                <label for="middle_name">Jina la Kati</label>
                <input type="text" id="middle_name" name="middle_name" class="form-control" autocomplete="additional-name" />
            </div>
            <div class="mb-3">
                <label for="last_name">Jina la Mwisho</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required autocomplete="family-name" />
            </div>
        </div>

        <!-- Step 2: Taarifa za Msingi -->
        <div class="step">
            <div class="mb-3">
                <label for="dob">Tarehe ya Kuzaliwa</label>
                <input type="date" id="dob" name="dob" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="gender">Jinsia</label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="">--Chagua--</option>
                    <option value="male">Mwanaume</option>
                    <option value="female">Mwanamke</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="marital_status">Hali ya Ndoa</label>
                <select id="marital_status" name="marital_status" class="form-select" required>
                    <option value="">--Chagua--</option>
                    <option value="single">Hajaoa/Hajaolewa</option>
                    <option value="married">Kaoa/Ameolewa</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="hasChildren" name="has_children" class="form-check-input" />
                <label class="form-check-label" for="hasChildren">Ana Watoto?</label>
            </div>
            <div id="childrenFields" style="display:none;">
                <div class="mb-3">
                    <label for="children_male">Idadi ya Watoto wa Kiume</label>
                    <input type="number" id="children_male" name="children_male" value="0" min="0" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="children_female">Idadi ya Watoto wa Kike</label>
                    <input type="number" id="children_female" name="children_female" value="0" min="0" class="form-control" />
                </div>
            </div>
        </div>

        <!-- Step 3: Makazi -->
        <div class="step">
            <div class="mb-3">
                <label for="country">Nchi</label>
                <select id="country" name="country" class="form-select" required>
                    <option value="Tanzania" selected>Tanzania</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Other">Nyingine</option>
                </select>
            </div>

            <div id="tz-fields">
                <div class="mb-3">
                    <label for="region">Mkoa</label>
                    <select id="region" name="region" class="form-select" required>
                        <option value="">--Chagua Mkoa--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="district">Wilaya</label>
                    <select id="district" name="district" class="form-select" required>
                        <option value="">--Chagua Wilaya--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ward">Kata</label>
                    <select id="ward" name="ward" class="form-select" required>
                        <option value="">--Chagua Kata--</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="village">Kijiji/Mtaa</label>
                    <select id="village" name="village" class="form-select" required>
                        <option value="">--Chagua Kijiji/Mtaa--</option>
                    </select>
                </div>
            </div>

            <div id="other-country" style="display:none;">
                <div class="mb-3">
                    <label for="city">Mji/Jiji</label>
                    <input type="text" id="city" name="city" class="form-control" placeholder="Andika Mji au Jiji" />
                </div>
            </div>
        </div>

        <!-- Step 4: Mawasiliano & Mwingineyo -->
        <div class="step">
            <div class="mb-3">
                <label for="phone">Namba ya Simu</label>
                <input type="text" id="phone" name="phone" class="form-control" required placeholder="Andika namba ya simu" />
            </div>
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Andika barua pepe" />
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Weka nenosiri" />
            </div>
            <div class="mb-3">
                <label for="parent_id">Mzazi (Parent ID)</label>
                <input type="number" id="parent_id" name="parent_id" class="form-control" placeholder="Weka ID ya mzazi kama ipo" />
            </div>
            <div class="mb-3">
                <label for="photo">Picha</label>
                <input type="file" id="photo" name="photo" class="form-control" />
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

    function fillRegions() {
        regionSelect.innerHTML = '<option value="">--Chagua Mkoa--</option>';
        for (let region in data) {
            regionSelect.innerHTML += `<option value="${region}">${region}</option>`;
        }
        districtSelect.innerHTML = '<option value="">--Chagua Wilaya--</option>';
        wardSelect.innerHTML = '<option value="">--Chagua Kata--</option>';
        villageSelect.innerHTML = '<option value="">--Chagua Kijiji/Mtaa--</option>';
    }

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

    countrySelect.addEventListener("change", function() {
        if (this.value === "Tanzania") {
            tzFields.style.display = "block";
            otherCountryFields.style.display = "none";
        } else {
            tzFields.style.display = "none";
            otherCountryFields.style.display = "block";
        }
    });

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

    // Steps control
    const steps = document.querySelectorAll(".step");
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, i) => s.classList.toggle("active", i === step));
        window.scrollTo(0, 0);
    }

    function validateStep(step) {
        const inputs = steps[step].querySelectorAll("input[required], select[required]");
        for (let input of inputs) {
            if (!input.value) return false;
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
