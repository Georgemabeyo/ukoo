<?php
include 'config.php'; // Ensure $conn is your DB connection
session_start();

$isLoggedIn = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$ADMIN_PASS = 'Makomelelo';

// Admin login
if (!$isLoggedIn) {
    if (isset($_POST['pass']) && $_POST['pass'] === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        $isLoggedIn = true;
    } else {
        ?>
        <form method="post" class="mx-auto mt-5" style="max-width:300px;">
            <h3 class="mb-3">Admin Login</h3>
            <input type="password" name="pass" class="form-control mb-3" placeholder="Password" required autofocus />
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <?php
        exit;
    }
}

$message = '';

// Handle delete user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $res = pg_query_params($conn, "DELETE FROM family_tree WHERE id = $1", [$id]);
    $message = $res ? "Mtu amefutwa kwa mafanikio." : "Imeshindikana kufuta mtu: " . pg_last_error($conn);
}

// Toggle admin rights
if (isset($_GET['toggle_admin'])) {
    $id = (int)$_GET['toggle_admin'];
    $result = pg_query_params($conn, "SELECT is_admin FROM family_tree WHERE id = $1", [$id]);
    if ($row = pg_fetch_assoc($result)) {
        $newAdmin = ($row['is_admin'] === 't') ? 'f' : 't';
        $updateResult = pg_query_params($conn, "UPDATE family_tree SET is_admin = $1 WHERE id = $2", [$newAdmin, $id]);
        $message = $updateResult ? ($newAdmin === 't' ? "Mtu amefanyiwa Admin." : "Mtu ameondolewa kama Admin.") : "Tatizo kubadilisha admin: " . pg_last_error($conn);
    }
}

// Publish new event
if (isset($_POST['publish_event'])) {
    $title = trim($_POST['event_title'] ?? '');
    $description = trim($_POST['event_description'] ?? '');
    if ($title === '' || $description === '') {
        $message = "Tafadhali jaza kichwa na maelezo ya tukio.";
    } else {
        $res = pg_query_params($conn, "INSERT INTO events (title, description, created_at) VALUES ($1, $2, NOW())", [$title, $description]);
        $message = $res ? "Tukio limechapishwa kwa mafanikio." : "Tatizo la kuchapisha tukio: " . pg_last_error($conn);
    }
}

// Send bulk SMS
if (isset($_POST['send_sms'])) {
    $sms_message = trim($_POST['sms_message'] ?? '');
    if ($sms_message === '') {
        $message = "Tafadhali andika ujumbe wa SMS.";
    } else {
        // Example: get all phone numbers and send SMS (integration needed)
        $res = pg_query($conn, "SELECT phone FROM family_tree WHERE phone IS NOT NULL AND phone != ''");
        $phones = [];
        if ($res) {
            while ($row = pg_fetch_assoc($res)) {
                $phones[] = $row['phone'];
            }
            // TODO: Integrate with SMS API here
            // For demo, just simulate success
            $message = "SMS imetumwa kwa " . count($phones) . " wanajamii.";
        } else {
            $message = "Tatizo la kupata nambari za simu: " . pg_last_error($conn);
        }
    }
}

// Fetch all people for admin list
$result = pg_query($conn, "SELECT * FROM family_tree ORDER BY first_name, last_name");
$people = $result ? pg_fetch_all($result) : [];
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Family Tree Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
</head>
<body>
<?php include 'header.php'; ?>
<div class="container my-5">
    <h1>Family Tree Admin Panel</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Publish Event -->
    <section class="mb-5">
        <h3>Chapisha Tukio Jipya</h3>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label for="event_title" class="form-label">Kichwa cha Tukio</label>
                <input type="text" id="event_title" name="event_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="event_description" class="form-label">Maelezo ya Tukio</label>
                <textarea id="event_description" name="event_description" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" name="publish_event" class="btn btn-primary">Chapisha Tukio</button>
        </form>
    </section>

    <!-- Bulk SMS -->
    <section class="mb-5">
        <h3>Tuma SMS kwa Wanajamii</h3>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label for="sms_message" class="form-label">Ujumbe wa SMS</label>
                <textarea id="sms_message" name="sms_message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" name="send_sms" class="btn btn-warning">Tuma SMS</button>
        </form>
    </section>

    <!-- Family Members List -->
    <section>
        <div class="mb-3">
            <a href="registration.php" class="btn btn-success">Sajili Mtu Mpya</a>
        </div>
        <table class="table table-striped table-bordered align-middle">
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
                <tr><td colspan="5" class="text-center">Hakuna taarifa za familia.</td></tr>
            <?php else: foreach ($people as $person): ?>
                <tr>
                    <td><?= htmlspecialchars(trim($person['first_name'] . ' ' . $person['middle_name'] . ' ' . $person['last_name'])) ?></td>
                    <td><?= htmlspecialchars($person['dob'] ?? '') ?></td>
                    <td><?= htmlspecialchars($person['gender'] ?? '') ?></td>
                    <td><?= ($person['is_admin'] ?? 'f') === 't' ? 'Ndiyo' : 'Hapana' ?></td>
                    <td>
                        <a href="admin.php?toggle_admin=<?= $person['id'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Kubadilisha ruhusa ya admin?')">
                            <?= ($person['is_admin'] ?? 'f') === 't' ? 'Ondoa Admin' : 'Fanya Admin' ?>
                        </a>
                        <a href="admin.php?delete=<?= $person['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Unataka kumfuta mtu?')">
                            Futa
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </section>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
