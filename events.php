<?php
session_start();
$dataFile = __DIR__ . '/events.json';
$message = '';

// Soma data zote zilizopo
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
    $desc = trim($_POST['desc'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $read_more_link = trim($_POST['read_more_link'] ?? '#');

    // Validation ya msingi: tumia == si =
    if ($title == '' || $date == '') {
        $message = "Tafadhali jaza vichwa muhimu kama Title na Date.";
    } else {
        $newEvent = [
            'id' => time(),
            'title' => $title,
            'date' => $date,
            'desc' => $desc,
            'photo' => $photo,
            'read_more_link' => $read_more_link
        ];
        $events[] = $newEvent;
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
<?php include 'header.php'; ?>

<div class="container">
  <h1>Ongeza Taarifa Mpya</h1>
  <?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="post" action="taarifa.php">
    <label>Title*:<br><input type="text" name="title" value="<?=htmlspecialchars($_POST['title'] ?? '')?>" required></label><br><br>
    <label>Date*:<br><input type="date" name="date" value="<?=htmlspecialchars($_POST['date'] ?? '')?>" required></label><br><br>
    <label>Description:<br><textarea name="desc"><?=htmlspecialchars($_POST['desc'] ?? '')?></textarea></label><br><br>
    <label>Photo URL:<br><input type="url" name="photo" value="<?=htmlspecialchars($_POST['photo'] ?? '')?>"></label><br><br>
    <label>Read More Link:<br><input type="url" name="read_more_link" value="<?=htmlspecialchars($_POST['read_more_link'] ?? '#')?>"></label><br><br>
    <button type="submit">Ongeza Taarifa</button>
  </form>
</div>

<?php include 'footer.php'; ?>

<script src="scripts.js"></script>
</body>
</html>
