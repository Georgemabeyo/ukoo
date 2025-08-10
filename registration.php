<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize and assign inputs (for brevity, I leave as is; in production sanitize)
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
        max-width: 600px;
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
        user-select: none;
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
    input[type=text]:focus, input[type=email]:focus, input[type=password]:focus,
    input[type=number]:focus, input[type=date]:focus, select:focus, input[type=file]:focus {
        border-color: #0d47a1;
        box-shadow: 0 0 10px #0d47a1aa;
    }
    .form-check-label {
        user-select: none;
        font-weight: 700;
        color: #0d47a1;
    }
    .form-check-input {
        transform: scale(1.2);
        margin-right: 12px;
        cursor: pointer;
    }
    #childrenFields {
        margin-left: 20px;
        border-left: 3px solid #9face6;
        padding-left: 20px;
        margin-top: 15px;
        background: #f0f6fc;
        border-radius: 10px;
    }

    /* Progress bar */
    .progress-container {
        width: 100%;
        background: #e1e9f6;
        border-radius: 20px;
        height: 14px;
        margin-bottom: 40px;
        box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
    }
    .progress-bar {
        height: 14px;
        background: #0d47a1;
        width: 0;
        border-radius: 20px;
        transition: width 0.4s ease;
    }

    /* Buttons */
    .btn-group {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    button {
        padding: 12px 30px;
        font-weight: 700;
        border-radius: 12px;
        border: none;
        font-size: 1.15rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgb(13 71 161 / 0.4);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        user-select: none;
        flex: 1;
        margin: 0 5px;
    }
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }
    .btn-next {
        background-color: #0d47a1;
        color: #fff;
    }
    .btn-next:hover:not(:disabled),
    .btn-next:focus:not(:disabled) {
        background-color: #074078;
        box-shadow: 0 6px 18px #074078aa;
        outline: none;
    }
    .btn-prev {
        background-color: #9face6;
        color: #0d47a1;
    }
    .btn-prev:hover:not(:disabled),
    .btn-prev:focus:not(:disabled) {
        background-color: #7a94c3;
        box-shadow: 0 6px 18px #7a94c3aa;
        outline: none;
    }
    .btn-submit {
        background-color: #2e7d32;
        color: white;
        margin-top: 20px;
        width: 100%;
        box-shadow: 0 6px 22px #2e7d3299;
    }
    .btn-submit:hover,
    .btn-submit:focus {
        background-color: #1b4f20;
        box-shadow: 0 8px 28px #1b4f2099;
        outline: none;
    }

    /* Back home link */
    .back-home-btn {
        display: inline-block;
        margin-bottom: 25px;
        font-weight: 700;
        color: #0d47a1;
        text-decoration: none;
        transition: color 0.3s ease;
        user-select: none;
    }
    .back-home-btn:hover,
    .back-home-btn:focus {
        color: #074078;
        outline: none;
    }

    /* Responsive */
    @media(max-width: 480px) {
        button {
            font-size: 1rem;
            padding: 10px 20px;
        }
        .btn-group {
            flex-direction: column;
        }
        .btn-group button {
            margin: 8px 0;
        }
        #childrenFields {
            margin-left: 10px;
            padding-left: 15px;
        }
    }
</style>
</head>
<body>

<div class="container" role="main" aria-label="Form ya usajili wa Ukoo wa Makomelelo">

    <h2>Usajili wa Ukoo wa Makomelelo</h2>

    <a href="index.php" class="back-home-btn" aria-label="Rudi Nyumbani">&larr; Nyumbani</a>

    <div class="progress-container" aria-hidden="true">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <form method="post" enctype="multipart/form-data" id="registrationForm" novalidate>

        <!-- Step 1 -->
        <div class="step active" aria-label="Hatua ya 1 - Majina">
            <label for="first_name">Jina la Kwanza <span aria-hidden="true" style="color:#d33;">*</span></label>
            <input type="text" id="first_name" name="first_name" required autocomplete="given-name" />

            <label for="middle_name">Jina la Kati</label>
            <input type="text" id="middle_name" name="middle_name" autocomplete="additional-name" />

            <label for="last_name">Jina la Mwisho <span aria-hidden="true" style="color:#d33;">*</span></label>
            <input type="text" id="last_name" name="last_name" required autocomplete="family-name" />
        </div>

        <!-- Step 2 -->
        <div class="step" aria-label="Hatua ya 2 - Taarifa za Msingi">
            <label for="dob">Tarehe ya Kuzaliwa <span aria-hidden="true" style="color:#d33;">*</span></label>
            <input type="date" id="dob" name="dob" required />

            <label for="gender">Jinsia <span aria-hidden="true" style="color:#d33;">*</span></label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>--Chagua--</option>
                <option value="male">Mwanaume</option>
                <option value="female">Mwanamke</option>
            </select>

            <label for="marital_status">Hali ya Ndoa <span aria-hidden="true" style="color:#d33;">*</span></label>
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
        <div class="step" aria-label="Hatua ya 3 - Makazi">
            <label for="country">Nchi <span aria-hidden="true" style="color:#d33;">*</span></label>
            <select id="country" name="country" required>
                <option value="Tanzania" selected>Tanzania</option>
                <option value="Kenya">Kenya</option>
                <option value="Uganda">Uganda</option>
                <option value="Other">Nyingine</option>
            </select>

            <div id="tz-fields">
                <label for="region">Mkoa <span aria-hidden="true" style="color:#d33;">*</span></label>
                <select id="region" name="region" required>
                    <option value="">--Chagua Mkoa--</option>
                </select>

                <label for="district">Wilaya <span aria-hidden="true" style="color:#d33;">*</span></label>
                <select id="district" name="district" required>
                    <option value="">--Chagua Wilaya--</option>
                </select>

                <label for="ward">Kata <span aria-hidden="true" style="color:#d33;">*</span></label>
                <select id="ward" name="ward" required>
                    <option value="">--Chagua Kata--</option>
                </select>

                <label for="village">Kijiji/Mtaa <span aria-hidden="true" style="color:#d33;">*</span></label>
                <select id="village" name="village" required>
                    <option value="">--Chagua Kijiji/Mtaa--</option>
                </select>
            </div>

            <div id="other-country" style="display:none; margin-top:10px;">
                <label for="city">Mji/Jiji</label>
                <input type="text" id="city" name="city" placeholder="Andika Mji au Jiji" />
            </div>
        </div>

        <!-- Step 4 -->
        <div class="step" aria-label="Hatua ya 4 - Mawasiliano & Mwingineyo">
            <label for="phone">Namba ya Simu <span aria-hidden="true" style="color:#d33;">*</span></label>
            <input type="text" id="phone" name="phone" required placeholder="Andika namba ya simu" />

            <label for="email">Email <span aria-hidden="true" style="color:#d33;">*</span></label>
            <input type="email" id="email" name="email" required placeholder="Andika barua pepe" />

            <label for="password">Password <span aria-hidden="true" style="color:#d33;">*</span></label>
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
    const hasChildrenCheckbox = document.getElementById("hasChildren");
    const childrenFields = document.getElementById("childrenFields");

    // Fill regions on load
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
            // Make required
            regionSelect.required = true;
            districtSelect.required = true;
            wardSelect.required = true;
            villageSelect.required = true;
            document.getElementById("city").required = false;
        } else {
            tzFields.style.display = "none";
            otherCountryFields.style.display = "block";
            regionSelect.required = false;
            districtSelect.required = false;
            wardSelect.required = false;
            villageSelect.required = false;
            document.getElementById("city").required = true;
        }
    });

    hasChildrenCheckbox.addEventListener("change", () => {
        childrenFields.style.display = hasChildrenCheckbox.checked ? "block" : "none";
        if (hasChildrenCheckbox.checked) {
            document.getElementById("children_male").required = true;
            document.getElementById("children_female").required = true;
        } else {
            document.getElementById("children_male").required = false;
            document.getElementById("children_female").required = false;
        }
    });

    window.addEventListener("DOMContentLoaded", () => {
        fillRegions();
        // Setup initial country fields state
        if (countrySelect.value === "Tanzania") {
            tzFields.style.display = "block";
            otherCountryFields.style.display = "none";
        } else {
            tzFields.style.display = "none";
            otherCountryFields.style.display = "block";
        }
        // Setup children fields visibility
        childrenFields.style.display = hasChildrenCheckbox.checked ? "block" : "none";
    });

    // Multi-step form control
    const steps = document.querySelectorAll(".step");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const submitBtn = document.getElementById("submitBtn");
    const progressBar = document.getElementById("progressBar");
    let currentStep = 0;

    function showStep(n) {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === n);
        });
        prevBtn.disabled = (n === 0);
        if (n === steps.length -1) {
            nextBtn.style.display = "none";
            submitBtn.style.display = "block";
        } else {
            nextBtn.style.display = "inline-block";
            submitBtn.style.display = "none";
        }
        // Update progress bar
        const percent = ((n) / (steps.length - 1)) * 100;
        progressBar.style.width = percent + "%";
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function validateStep() {
        const inputs = steps[currentStep].querySelectorAll("input[required], select[required]");
        for (let input of inputs) {
            if (!input.value) {
                input.focus();
                return false;
            }
            if (input.type === "number" && input.value < 0) {
                input.focus();
                return false;
            }
        }
        return true;
    }

    nextBtn.addEventListener("click", () => {
        if (!validateStep()) return;
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener("click", () => {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Initialize form display
    showStep(currentStep);
</script>
</body>
</html>
