<?php
session_start();
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
        <!DOCTYPE html>
        <html lang="sw">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1"/>
            <title>Admin Login</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
                <form method="post" class="bg-white p-4 rounded shadow-sm" style="width:320px;">
                    <h3 class="mb-3 text-center">Admin Login</h3>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <input type="password" name="pass" class="form-control mb-3" placeholder="Password" required>
                    <button type="submit" class="btn btn-primary w-100">Ingia</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

$dataFile = __DIR__ . '/events.json';
$message = '';

if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $events = json_decode($json, true);
    if (!is_array($events)) {
        $events = [];
    }
} else {
    $events = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_admin'])) {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $read_more_link = trim($_POST['read_more_link'] ?? '');

    if ($title == '' || $date == '') {
        $message = "Tafadhali jaza vichwa muhimu kama Title na Date.";
    } else {
        $newEvent = [
            'id' => time(),
            'title' => $title,
            'date' => $date,
            'description' => $description,
            'image' => $image,
            'read_more_link' => $read_more_link ? $read_more_link : '#'
        ];
        $events[] = $newEvent;
        if (file_put_contents($dataFile, json_encode($events, JSON_PRETTY_PRINT))) {
            $message = "Taarifa mpya imeongezwa kikamilifu.";
            $_POST = [];
        } else {
            $message = "Imeshindikana kuhifadhi taarifa mpya.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Admin - Ongeza Taarifa Mpya</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Admin Panel - Ongeza Taarifa Mpya</h1>
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Kichwa cha Tukio *</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Tarehe ya Tukio *</label>
            <input type="date" name="date" class="form-control" required value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Maelezo</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">URL ya Picha</label>
            <input type="url" name="image" class="form-control" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Link ya "Soma Zaidi" (hiari)</label>
            <input type="url" name="read_more_link" class="form-control" value="<?= htmlspecialchars($_POST['read_more_link'] ?? '') ?>" placeholder="https://...">
        </div>
        <button type="submit" class="btn btn-primary">Ongeza Taarifa</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
