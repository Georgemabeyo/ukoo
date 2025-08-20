<?php
session_start();
include 'config.php'; // Unganisha database connection $conn

// Hakikisha mtu amelogin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$editEntry = null;

// Pata id ya mtu anayetaka kuhariri kutoka GET
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Kusoma taarifa za mtu huyo
    $result = pg_query_params($conn, "SELECT * FROM family_tree WHERE id=$1", [$id]);
    if ($result && pg_num_rows($result) === 1) {
        $editEntry = pg_fetch_assoc($result);
    } else {
        $message = "Mtumiaji haipatikani.";
    }
} else {
    $message = "Taarifa si sahihi.";
}

// Kufanya update data ikiwa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $marital_status = $_POST['marital_status'] ?? null;
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($first_name == '' || $last_name == '') {
        $message = "Jina la kwanza na la mwisho ni lazima kujazwa.";
    } else {
        $photo = $editEntry['photo'] ?? ''; // picha ya zamani kama ipo

        // Kuhandling picha mpya
        if (!empty($_FILES['photo']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                $target_dir = __DIR__ . "/uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $newFileName = time() . "_" . basename($_FILES["photo"]["name"]);
                $target_file = $target_dir . $newFileName;

                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    // Futa picha ya zamani, ikiwa ipo na si default
                    if (!empty($photo) && file_exists($target_dir . $photo)) {
                        unlink($target_dir . $photo);
                    }
                    $photo = $newFileName;
                } else {
                    $message = "Imeshindikana kupakia picha.";
                }
            } else {
                $message = "Aina ya picha haikubaliki. Tumia JPG, PNG, au GIF.";
            }
        }

        if (empty($message)) {
            $sql = "UPDATE family_tree SET first_name=$1, middle_name=$2, last_name=$3, dob=$4, gender=$5, marital_status=$6, phone=$7, email=$8, photo=$9 WHERE id=$10";
            $params = [$first_name, $middle_name, $last_name, $dob, $gender, $marital_status, $phone, $email, $photo, $id];
            $result = pg_query_params($conn, $sql, $params);
            if ($result) {
                $message = "Taarifa zimehifadhiwa.";
                // Update editEntry kwa data mpya
                $res = pg_query_params($conn, "SELECT * FROM family_tree WHERE id=$1", [$id]);
                if ($res && pg_num_rows($res) === 1) {
                    $editEntry = pg_fetch_assoc($res);
                }
            } else {
                $message = "Kosa katika kuhifadhi taarifa: " . pg_last_error($conn);
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
<title>Hariri Taarifa za Mtumiaji</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php include 'header.php'; ?>
<div class="container my-5">
    <h2>Hariri Taarifa za Mtumiaji</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($editEntry): ?>
    <form method="post" action="edit_member.php?id=<?= $editEntry['id'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editEntry['id'] ?>">

        <!-- Picha ya sasa -->
        <div class="mb-3">
            <label>Picha ya sasa</label><br>
            <?php if (!empty($editEntry['photo']) && file_exists(__DIR__ . "/uploads/" . $editEntry['photo'])): ?>
                <img src="uploads/<?= htmlspecialchars($editEntry['photo']) ?>" alt="Picha" style="max-width:150px; border-radius:50%;">
            <?php else: ?>
                <p>Hakuna picha.</p>
            <?php endif; ?>
        </div>

        <!-- Upload picha mpya -->
        <div class="mb-3">
            <label>Badilisha Picha</label>
            <input type="file" name="photo" accept="image/*" class="form-control">
            <small class="form-text text-muted">Tumia picha aina ya JPG, PNG, GIF.</small>
        </div>

        <div class="mb-3">
            <label>Jina la Kwanza</label>
            <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($editEntry['first_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Jina la Kati</label>
            <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($editEntry['middle_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Jina la Mwisho</label>
            <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($editEntry['last_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Tarehe ya Kuzaliwa</label>
            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($editEntry['dob']) ?>">
        </div>
        <div class="mb-3">
            <label>Jinsia</label>
            <select name="gender" class="form-select">
                <option value="">--Chagua--</option>
                <option value="male" <?= ($editEntry['gender'] == 'male') ? 'selected' : '' ?>>Mwanaume</option>
                <option value="female" <?= ($editEntry['gender'] == 'female') ? 'selected' : '' ?>>Mwanamke</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Hali ya Ndoa</label>
            <select name="marital_status" class="form-select">
                <option value="">--Chagua--</option>
                <option value="single" <?= ($editEntry['marital_status'] == 'single') ? 'selected' : '' ?>>Sijaoa/Sijaolewa</option>
                <option value="married" <?= ($editEntry['marital_status'] == 'married') ? 'selected' : '' ?>>Nimeoa/Nimeolewa</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Namba ya Simu</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($editEntry['phone']) ?>">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editEntry['email']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Hifadhi Mabadiliko</button>
        <a href="family_tree.php" class="btn btn-secondary">Rudi Ukoo</a>
    </form>
    <?php else: ?>
        <a href="family_tree.php" class="btn btn-primary">Rudi Ukoo</a>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
