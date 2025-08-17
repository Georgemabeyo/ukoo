<?php
$file = 'events.json';

// Pakua data kutoka file kama array
$events = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $events = json_decode($json, true);
    if (!is_array($events)) $events = [];
}

// Handle add new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    if ($name !== '') {
        $new_event = [
            'id' => time(), // unique id
            'name' => $name,
            'description' => $description
        ];
        $events[] = $new_event;
        file_put_contents($file, json_encode($events, JSON_PRETTY_PRINT));
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle delete event
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $events = array_filter($events, function($e) use ($delete_id) {
        return $e['id'] !== $delete_id;
    });
    file_put_contents($file, json_encode(array_values($events), JSON_PRETTY_PRINT));
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<title>Usimamizi wa Events (JSON based)</title>
<style>
body { font-family: Arial, sans-serif; max-width: 700px; margin: 2rem auto; background: #222; color: #fff; }
input, textarea, button { width: 100%; margin-bottom: 1rem; padding: 0.5rem; border-radius: 5px; }
input, textarea { border: none; }
button { background: #fff; color: #222; border: none; cursor: pointer; font-weight: bold; }
ul { list-style-type: none; padding: 0; }
li { padding: 10px; border-bottom: 1px solid #555; }
a.delete { color: #ff6666; text-decoration: none; font-weight: bold; }
a.delete:hover { text-decoration: underline; }
</style>
</head>
<body>

<h2>Ongeza Taarifa Mpya</h2>
<form method="post" action="">
    <input type="hidden" name="add" value="1" />
    <label>Jina la Event:</label>
    <input type="text" name="name" required />
    <label>Maelezo:</label>
    <textarea name="description" rows="3"></textarea>
    <button type="submit">Ongeza</button>
</form>

<h2>Orodha ya Events</h2>
<ul>
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <li>
                <strong><?= htmlspecialchars($event['name']) ?></strong><br/>
                <small><?= nl2br(htmlspecialchars($event['description'])) ?></small><br/>
                <a class="delete" href="?delete=<?= $event['id'] ?>" onclick="return confirm('Una uhakika unataka kufuta?')">Futa</a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>Hakuna events yoyote.</li>
    <?php endif; ?>
</ul>

</body>
</html>
