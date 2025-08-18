<?php
session_start();

// Password ya admin imara kwa mfano
$ADMIN_PASS = 'Makomelelo';

if (!isset($_SESSION['is_admin'])) {
    if (isset($_POST['pass'])) {
        if ($_POST['pass'] === $ADMIN_PASS) {
            $_SESSION['is_admin'] = true;
        } else {
            $error = "Password si sahihi!";
        }
    }
    if (!isset($_SESSION['is_admin'])) {
        ?>
        <form method="post" style="max-width:300px;margin:50px auto;">
            <h3>Admin Login</h3>
            <?php if (!empty($error)) echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>'; ?>
            <input type="password" name="pass" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
        <?php
        exit;
    }
}

$dataFile = __DIR__ . '/family_tree.json'; // au 'events.json'
$entries = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $entries = json_decode($json, true);
    if (!is_array($entries)) $entries = [];
}

$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $entries = array_filter($entries, fn($e) => $e['id'] !== $id);
    file_put_contents($dataFile, json_encode(array_values($entries), JSON_PRETTY_PRINT));
    header('Location: taarifa.php');
    exit;
}

// Handle make admin or revoke admin
if (isset($_GET['admin'])) {
    $id = (int)$_GET['admin'];
    foreach ($entries as &$entry) {
        if ($entry['id'] === $id) {
            $entry['is_admin'] = !($entry['is_admin'] ?? false);
            break;
        }
    }
    file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT));
    header('Location: taarifa.php');
    exit;
}

// Handle add or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $is_admin_flag = isset($_POST['is_admin']) ? true : false;

    if ($first_name === '' || $last_name === '') {
        $message = "Jaza majina ya kwanza na ya mwisho.";
    } else {
        if ($id) {
            // Update existing
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
            $message = "Mrekebisho umetekelezwa.";
        } else {
            // Add new
            $newEntry = [
                'id' => time(),
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'dob' => $dob,
                'gender' => $gender,
                'is_admin' => $is_admin_flag,
            ];
            $entries[] = $newEntry;
            $message = "Mtu mpya ameongezwa.";
        }
        file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT));
    }
}

// Kama kuna kuedit unakija na ?edit={id}
$editEntry = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    foreach ($entries as $e) {
        if ($e['id'] === $id) {
            $editEntry = $e;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin - Family Tree Management</title>
<style>
body { max-width: 900px; margin: 20px auto; font-family: Arial, sans-serif; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
.table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
.form-group { margin-bottom: 15px; }
label { display: block; font-weight: bold; }
input[type=text], input[type=date], select { width: 100%; padding: 6px; }
button { padding: 8px 15px; font-weight: bold; cursor: pointer; }
.success { color: green; margin-bottom: 20px; }
a.button-link { background: #007bff; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-right: 5px; }
a.button-link:hover { background: #0056b3; }
a.delete-link { color: #dc3545; cursor: pointer; text-decoration: underline; }
</style>
</head>
<body>

<h1>Family Tree Management - Admin Panel</h1>
<?php if ($message): ?>
    <p class="success"><?=htmlspecialchars($message)?></p>
<?php endif; ?>

<!-- Add/Edit Form -->
<form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($editEntry['id'] ?? '') ?>">
    <div class="form-group">
        <label>Jina la Kwanza</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($editEntry['first_name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>Jina la Kati</label>
        <input type="text" name="middle_name" value="<?= htmlspecialchars($editEntry['middle_name'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Jina la Mwisho</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($editEntry['last_name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>Tarehe ya Kuzaliwa</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($editEntry['dob'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Jinsia</label>
        <select name="gender">
            <option value="">-- Chagua --</option>
            <option value="male" <?= (isset($editEntry['gender']) && $editEntry['gender'] === 'male') ? 'selected' : '' ?>>Mwanaume</option>
            <option value="female" <?= (isset($editEntry['gender']) && $editEntry['gender'] === 'female') ? 'selected' : '' ?>>Mwanamke</option>
        </select>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_admin" <?= (!empty($editEntry['is_admin'])) ? 'checked' : '' ?>> Mmoja wa Admin</label>
    </div>
    <button type="submit"><?= $editEntry ? 'Hifadhi Mabadiliko' : 'Ongeza Mtumiaji Mpya' ?></button>
    <?php if ($editEntry): ?>
        <a href="taarifa.php">Ongeza mtu mpya</a>
    <?php endif; ?>
</form>

<!-- Table ya familia -->
<table class="table">
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
    <?php else: ?>
        <?php foreach ($entries as $entry): ?>
            <tr>
                <td>
                    <a href="?edit=<?= $entry['id'] ?>">
                        <?= htmlspecialchars(trim("{$entry['first_name']} {$entry['middle_name']} {$entry['last_name']}")) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($entry['dob'] ?? '') ?></td>
                <td><?= htmlspecialchars($entry['gender'] ?? '') ?></td>
                <td><?= !empty($entry['is_admin']) ? 'Ndiyo' : 'Hapana' ?></td>
                <td>
                    <a href="?admin=<?= $entry['id'] ?>" 
                       onclick="return confirm('Unataka kubadilisha ruhusa ya admin kwa mtu huyu?')"
                       class="button-link"><?= !empty($entry['is_admin']) ? 'Ondoa Admin' : 'Fanya Admin' ?></a>

                    <a href="?delete=<?= $entry['id'] ?>" 
                       onclick="return confirm('Una uhakika unataka kumfuta mtu huyu?')"
                       class="delete-link">Futa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
