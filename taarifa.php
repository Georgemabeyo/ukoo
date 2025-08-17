<?php
session_start();

// Password ya admin
$ADMIN_PASS = 'Makomelelo';

// Kuingia admin
if (!isset($_SESSION['is_admin'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
    } else {
        echo '<form method="post" style="max-width:300px;margin:50px auto;">
                <h3>Admin Login</h3>
                <input type="password" name="pass" placeholder="Password" required />
                <button type="submit">Login</button>
              </form>';
        exit;
    }
}

// Funguo za kusoma na kuandika data JSON
function read_data($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function write_data($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Hifadhi success message
$message = '';

// Fomu ya kuongeza ujumbe mpya
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_admin'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['desc']);

    $img_name = '';
    // Upload picha
    if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $img_name = time() . '_' . basename($_FILES['photo']['name']);
        $target_path = $upload_dir . $img_name;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $message = "Upload ya picha haikufanikiwa.";
            $img_name = '';
        }
    }

    if ($title !== '') {
        $file = 'events.json';
        $entries = read_data($file);
        $entries[] = [
            'id' => time(),
            'title' => $title,
            'desc' => $desc,
            'photo' => $img_name
        ];
        write_data($file, $entries);
        $message = "Taarifa imeandikwa kwa mafanikio!";
    } else {
        $message = "Jaza kichwa cha taarifa.";
    }
}

// Kufuta taarifa
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $entries = read_data('events.json');
    $entries = array_filter($entries, fn($e) => $e['id'] !== $delete_id);
    write_data('events.json', array_values($entries));
    header("Location: taarifa.php");
    exit;
}

// Pakua data za sasa
$events = read_data('events.json');
?>

<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<title>Admin - Taarifa Panel</title>
<style>
body { max-width: 700px; margin: 30px auto; font-family: Arial, sans-serif; background:#121212; color:#eee; padding:15px; }
form { background: #222; padding: 20px; border-radius: 10px; margin-bottom: 40px; }
input, textarea { width: 100%; margin: 10px 0; padding: 8px; background: #333; border: none; color: #eee; border-radius: 6px; }
button { background: #1e90ff; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 6px; font-weight: bold; }
button:hover { background: #015bb5; }
h2 { border-bottom: 1px solid #444; padding-bottom: 5px; }
img.upload-img { max-width: 60px; margin-right: 10px; vertical-align: middle; border-radius: 6px; }
.entry { margin-bottom: 15px; padding: 15px; background: #222; border-radius: 8px; }
.entry img { max-width: 150px; display: block; margin-top: 8px; border-radius: 6px; }
a.delete-link { color: #ff6666; text-decoration: none; font-weight: bold; float: right; }
a.delete-link:hover { text-decoration: underline; }
.message { margin-bottom: 20px; color: #66ff66; font-weight: bold; }
</style>
</head>
<body>

<h1>Admin Panel - Taarifa</h1>
<?php if ($message): ?>
  <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Kichwa cha Taarifa:</label>
    <input type="text" name="title" required />

    <label>Maelezo:</label>
    <textarea name="desc" rows="4" required></textarea>

    <label>Upload Picha (hiari):</label>
    <input type="file" name="photo" accept="image/*" />

    <button type="submit" name="submit_admin">Andika Taarifa</button>
</form>

<h2>Taarifa Zilizopo</h2>
<?php if (count($events) > 0): ?>
    <?php foreach ($events as $entry): ?>
        <div class="entry">
          <a href="?delete=<?= $entry['id'] ?>" class="delete-link" onclick="return confirm('Una uhakika unataka kufuta?')">Futa</a>
          <strong><?= htmlspecialchars($entry['title']) ?></strong>
          <p><?= nl2br(htmlspecialchars($entry['desc'])) ?></p>
          <?php if ($entry['photo']): ?>
            <img src="uploads/<?= htmlspecialchars($entry['photo']) ?>" alt="Taarifa Picha" />
          <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
  <p>Hakuna taarifa za kuonyesha.</p>
<?php endif; ?>

</body>
</html>
