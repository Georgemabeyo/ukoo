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
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssiiissssssssssi",
        $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
        $has_children, $children_male, $children_female, $country, $region, $district,
        $ward, $village, $city, $phone, $email, $password, $photo, $parent_id
    );

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>
                Usajili umefanikiwa! <a href='family_tree.php'>Angalia ukoo</a>
              </div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Kuna tatizo: " . $stmt->error . "</div>";
    }
    $stmt->close();
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
            <button type="button" class="btn btn-primary next">Endelea</button>
        </div>

        <div class="step">
            <div class="mb-3">
                <label>Jina la Kati</label>
                <input type="text" name="middle_name" class="form-control">
            </div>
            <button type="button" class="btn btn-secondary prev">Rudi</button>
            <button type="button" class="btn btn-primary next">Endelea</button>
        </div>

        <div class="step">
            <div class="mb-3">
                <label>Jina la Mwisho</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <button type="button" class="btn btn-secondary prev">Rudi</button>
            <button type="button" class="btn btn-primary next">Endelea</button>
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
                    <option value="male">Mwanaume</option>
                    <option value="female">Mwanamke</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Hali ya Ndoa</label>
                <select name="marital_status" class="form-select" required>
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
            <button type="button" class="btn btn-secondary prev">Rudi</button>
            <button type="button" class="btn btn-primary next">Endelea</button>
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
                    <select name="region" id="region" class="form-select">
                        <option value="">--Chagua Mkoa--</option>
                        <option value="Dar es Salaam">Dar es Salaam</option>
                        <option value="Dodoma">Dodoma</option>
                        <!-- Unaweza kuongeza mikoa mingine hapa -->
                    </select>
                </div>
                <div class="mb-3">
                    <label>Wilaya</label>
                    <input type="text" name="district" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Kata</label>
                    <input type="text" name="ward" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Kijiji/Mtaa</label>
                    <input type="text" name="village" class="form-control">
                </div>
            </div>

            <div id="other-country" style="display:none;">
                <div class="mb-3">
                    <label>Mji/Jiji</label>
                    <input type="text" name="city" class="form-control">
                </div>
            </div>

            <button type="button" class="btn btn-secondary prev">Rudi</button>
            <button type="button" class="btn btn-primary next">Endelea</button>
        </div>

        <!-- Step 4: Mawasiliano & Mwingineyo -->
        <div class="step">
            <div class="mb-3">
                <label>Namba ya Simu</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mzazi (Parent ID)</label>
                <input type="number" name="parent_id" class="form-control" placeholder="Weka ID ya mzazi kama ipo">
            </div>
            <div class="mb-3">
                <label>Picha</label>
                <input type="file" name="photo" class="form-control">
            </div>
            <button type="button" class="btn btn-secondary prev">Rudi</button>
            <button type="submit" class="btn btn-success">Sajili</button>
        </div>

    </form>
</div>

<script>
    let steps = document.querySelectorAll(".step");
    let currentStep = 0;

    document.querySelectorAll(".next").forEach(btn => {
        btn.addEventListener("click", () => {
            // Validate required fields before moving next
            let inputs = steps[currentStep].querySelectorAll("input, select");
            for (let input of inputs) {
                if (input.hasAttribute("required") && !input.value) {
                    alert("Tafadhali jaza fomu yote muhimu kabla ya kuendelea.");
                    return;
                }
            }

            steps[currentStep].classList.remove("active");
            currentStep++;
            steps[currentStep].classList.add("active");
            window.scrollTo(0,0);
        });
    });

    document.querySelectorAll(".prev").forEach(btn => {
        btn.addEventListener("click", () => {
            steps[currentStep].classList.remove("active");
            currentStep--;
            steps[currentStep].classList.add("active");
            window.scrollTo(0,0);
        });
    });

    // Show/hide children count fields
    document.getElementById("hasChildren").addEventListener("change", function() {
        document.getElementById("childrenFields").style.display = this.checked ? "block" : "none";
    });

    // Show/hide makazi fields based on country
    document.getElementById("country").addEventListener("change", function() {
        if (this.value === "Tanzania") {
            document.getElementById("tz-fields").style.display = "block";
            document.getElementById("other-country").style.display = "none";
        } else {
            document.getElementById("tz-fields").style.display = "none";
            document.getElementById("other-country").style.display = "block";
        }
    });

    // Initialize display based on default selection
    window.addEventListener("load", () => {
        let countrySelect = document.getElementById("country");
        if (countrySelect.value === "Tanzania") {
            document.getElementById("tz-fields").style.display = "block";
            document.getElementById("other-country").style.display = "none";
        } else {
            document.getElementById("tz-fields").style.display = "none";
            document.getElementById("other-country").style.display = "block";
        }
    });
</script>
</body>
</html>
