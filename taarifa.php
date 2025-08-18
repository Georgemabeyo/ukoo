<?php
include 'config.php'; // Hii ni ku-connect database $conn
session_start();

$isLoggedIn = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$ADMIN_PASS = 'Makomelelo';

if (!$isLoggedIn) {
    if (isset($_POST['pass']) && $_POST['pass'] === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        $isLoggedIn = true;
    } else {
        ?>
        <form method="post" style="max-width:300px;margin:50px auto;">
            <h3>Admin Login</h3>
            <input type="password" name="pass" placeholder="Password" required autofocus />
            <button type="submit">Login</button>
        </form>
        <?php exit;
    }
}

$message = '';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $res = pg_query_params($conn, "DELETE FROM family_tree WHERE id = $1", [$id]);
    $message = $res ? "Mtu amefutwa kwa mafanikio." : "Imeshindikana kufuta mtu: " . pg_last_error($conn);
}

if (isset($_GET['toggle_admin'])) {
    $id = (int)$_GET['toggle_admin'];
    $result = pg_query_params($conn, "SELECT is_admin FROM family_tree WHERE id = $1", [$id]);
    if ($row = pg_fetch_assoc($result)) {
        $newAdmin = ($row['is_admin'] === 't') ? 'f' : 't';
        $updateResult = pg_query_params($conn, "UPDATE family_tree SET is_admin = $1 WHERE id = $2", [$newAdmin, $id]);
        $message = $updateResult ? ($newAdmin === 't' ? "Mtu amefanyiwa Admin." : "Mtu ameondolewa kama Admin.") : "Tatizo kubadilisha admin: " . pg_last_error($conn);
    }
}

$editEntry = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = pg_query_params($conn, "SELECT * FROM family_tree WHERE id = $1", [$id]);
    $editEntry = pg_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $is_admin = isset($_POST['is_admin']);

    if ($first_name === '' || $last_name === '') {
        $message = "Jaza majina ya kwanza na ya mwisho.";
    } else {
        $res = pg_query_params($conn, "UPDATE family_tree SET first_name=$1, middle_name=$2, last_name=$3, dob=$4, gender=$5, is_admin=$6 WHERE id=$7",
            [$first_name, $middle_name, $last_name, $dob, $gender, $is_admin, $id]);
        if ($res) {
            $message = "Mabadiliko yametunzwa.";
            $result = pg_query_params($conn, "SELECT * FROM family_tree WHERE id = $1", [$id]);
            $editEntry = pg_fetch_assoc($result);
        } else {
            $message = "Tatizo kuhifadhi mabadiliko: " . pg_last_error($conn);
        }
    }
}

$result = pg_query($conn, "SELECT * FROM family_tree ORDER BY first_name, last_name");
$people = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $people[] = $row;
    }
} else {
    $message = "Tatizo kuchukua data: " . pg_last_error($conn);
}

include 'header.php';
?>

<div class="container my-5">
    <h1>Family Tree Admin Panel</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="mb-3">
        <a href="registration.php" class="btn btn-success">Sajili Mtu Mpya</a>
    </div>

    <?php if ($editEntry): ?>
    <form method="post" class="mb-4">
        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editEntry['id']) ?>">
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
                <option value="male" <?= (isset($editEntry['gender']) && $editEntry['gender'] === 'male') ? 'selected' : '' ?>>Mwanaume</option>
                <option value="female" <?= (isset($editEntry['gender']) && $editEntry['gender'] === 'female') ? 'selected' : '' ?>>Mwanamke</option>
            </select>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_admin" class="form-check-input" id="isAdmin" <?= !empty($editEntry['is_admin']) ? 'checked' : '' ?>>
            <label for="isAdmin" class="form-check-label">Mmoja wa Admin</label>
        </div>
        <button type="submit" class="btn btn-primary">Hifadhi Mabadiliko</button>
        <a href="taarifa.php" class="btn btn-secondary ms-2">Ongeza Mtu Mpya</a>
    </form>
    <?php endif; ?>

    <table class="table table-striped table-bordered">
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
            <?php if (empty($people)): ?>
            <tr><td colspan="5">Hakuna taarifa za familia.</td></tr>
            <?php else: foreach ($people as $person): ?>
            <tr>
                <td><a href="taarifa.php?edit=<?= $person['id'] ?>"><?= htmlspecialchars(trim($person['first_name'] . ' ' .  $person['middle_name'] . ' ' . $person['last_name'])) ?></a></td>
                <td><?= htmlspecialchars($person['dob'] ?? '') ?></td>
                <td><?= htmlspecialchars($person['gender'] ?? '') ?></td>
                <td><?= ($person['is_admin'] ?? 'f') == 't' ? 'Ndiyo' : 'Hapana' ?></td>
                <td>
                    <a href="taarifa.php?toggle_admin=<?= $person['id'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Kubadilisha ruhusa ya admin?')">
                        <?= ($person['is_admin'] ?? 'f') == 't' ? 'Ondoa Admin' : 'Fanya Admin' ?>
                    </a>
                    <a href="taarifa.php?delete=<?= $person['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Unataka kumfuta mtu?')">
                        Futa
                    </a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
