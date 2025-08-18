<?php
session_start();
$ADMIN_PASS = 'Makomelelo';

if (!isset($_SESSION['is_admin'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
    } else {
        ?>
        <form method="post" style="max-width:300px;margin:50px auto;">
            <h3>Admin Login</h3>
            <input type="password" name="pass" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
        <?php
        exit;
    }
}

include 'header.php';

$dataFile = __DIR__ . '/family_tree.json';
$entries = [];
if (file_exists($dataFile)) {
    $entries = json_decode(file_get_contents($dataFile), true) ?: [];
}

$message = '';

// Delete entry
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $entries = array_filter($entries, fn($e) => $e['id'] !== $id);
    file_put_contents($dataFile, json_encode(array_values($entries), JSON_PRETTY_PRINT));
    header('Location: taarifa.php');
    exit;
}

// Toggle admin rights
if (isset($_GET['toggle_admin'])) {
    $id = (int)$_GET['toggle_admin'];
    foreach ($entries as &$entry) {
        if ($entry['id'] === $id) {
            $entry['is_admin'] = empty($entry['is_admin']);
            break;
        }
    }
    unset($entry);
    file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT));
    header('Location: taarifa.php');
    exit;
}

// Redirect to registration.php to add new person
if (isset($_GET['register_new'])) {
    header('Location: registration.php');
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $is_admin_flag = isset($_POST['is_admin']);

    if ($first_name === '' || $last_name === '') {
        $message = "Jaza majina ya kwanza na ya mwisho.";
    } else {
        foreach ($entries as &$entry) {
            if ($entry['id'] === $id) {
                $entry['first_name'] = $first_name;
                $entry['middle_name'] = $middle_name;
                $entry['last_name'] = $last_name;
                $entry['dob'] = $dob;
                $entry['gender'] = $gender;
                $entry['is_admin'] = $is_admin_flag;
                break;
            }
        }
        unset($entry);
        file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT));
        $message = "Mrekebisho umetekelezwa.";
    }
}


// Pre-fill form data for editing
$editEntry = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($entries as $e) {
        if ($e['id'] === $editId) {
            $editEntry = $e;
            break;
        }
    }
}
?>
<div class="container my-5">
    <h1>Family Tree Admin Panel</h1>
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="?register_new=1" class="btn btn-primary">Sajili Mtu Mpya</a>
    </div>

    <?php if ($editEntry): ?>
    <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editEntry['id']) ?>">
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
                <option value="">-- Chagua --</option>
                <option value="male" <?= $editEntry['gender'] === 'male' ? 'selected' : '' ?>>Mwanaume</option>
                <option value="female" <?= $editEntry['gender'] === 'female' ? 'selected' : '' ?>>Mwanamke</option>
            </select>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="is_admin" id="isAdmin" <?= !empty($editEntry['is_admin']) ? 'checked' : '' ?>>
            <label for="isAdmin" class="form-check-label">Mmoja wa Admin</label>
        </div>
        <button type="submit" class="btn btn-success">Hifadhi Mabadiliko</button>
        <a href="taarifa.php" class="btn btn-secondary ms-2">Ongeza Mtu Mpya</a>
    </form>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Jina Kamili</th>
                <th>Tarehe ya Kuzaliwa</th>
                <th>Jinsia</th>
                <th>Admin</th>
                <th>Vitendo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($entries)): ?>
                <tr><td colspan="5">Hakuna taarifa za familia.</td></tr>
            <?php else: foreach ($entries as $entry): ?>
                <tr>
                    <td><a href="?edit=<?= $entry['id'] ?>"><?= htmlspecialchars(trim("{$entry['first_name']} {$entry['middle_name']} {$entry['last_name']}")) ?></a></td>
                    <td><?= htmlspecialchars($entry['dob'] ?? '') ?></td>
                    <td><?= htmlspecialchars($entry['gender'] ?? '') ?></td>
                    <td><?= !empty($entry['is_admin']) ? 'Ndiyo' : 'Hapana' ?></td>
                    <td>
                        <a href="?toggle_admin=<?= $entry['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Kubadilisha ruhusa ya admin?')">
                            <?= !empty($entry['is_admin']) ? 'Ondoa Admin' : 'Fanya Admin' ?>
                        </a>
                        <a href="?delete=<?= $entry['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Unataka kumfuta mtu?')">Futa</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
