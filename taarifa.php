<?php
session_start();

$dataFile = __DIR__ . '/events.json';
$message = '';

// Soma data zilizopo kwenye faili
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $events = json_decode($json, true);
    if (!is_array($events)) {
        $events = [];
    }
} else {
    $events = [];
}

// Ongeza taarifa mpya baada ya POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $read_more_link = trim($_POST['read_more_link'] ?? '#');

    // Validation ya msingi
    if ($title == '' || $date == '') {
        $message = "Tafadhali jaza vichwa muhimu kama Title na Date.";
    } else {
        $newEvent = [
            'id' => time(),
            'title' => $title,
            'date' => $date,
            'description' => $description,
            'image' => $image,
            'read_more_link' => $read_more_link
        ];
        $events[] = $newEvent;
        // Andika taarifa zote pamoja kwenye faili
        if (file_put_contents($dataFile, json_encode($events, JSON_PRETTY_PRINT))) {
            $message = "Taarifa mpya imeongezwa kikamilifu.";
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ongeza Taarifa Mpya</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<div class="container">
    <h1>Ongeza Taarifa Mpya</h1>
    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="taarifa.php">
        <label>Kichwa cha Tukio:*<br>
            <input type="text" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </label><br><br>

        <label>Tarehe ya Tukio:*<br>
            <input type="date" name="date" required value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
        </label><br><br>

        <label>Maelezo:<br>
            <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </label><br><br>

        <label>URL ya Picha:<br>
            <input type="url" name="image" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
        </label><br><br>

        <label>Link ya "Soma Zaidi":<br>
            <input type="url" name="read_more_link" value="<?= htmlspecialchars($_POST['read_more_link'] ?? '#') ?>">
        </label><br><br>

        <button type="submit">Ongeza Taarifa</button>
    </form>
</div>
</body>
</html>
