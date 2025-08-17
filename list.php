<?php
include 'config.php';

$msg = $_GET['msg'] ?? '';

$res = pg_query($conn, "SELECT id, full_name, ward FROM persons ORDER BY full_name");
$persons = [];
if ($res) {
    while ($row = pg_fetch_assoc($res)) {
        $persons[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <title>Orodha ya Watu</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="dark-mode">
    <h2>Orodha ya Watu</h2>
    <?php if ($msg): ?>
        <p class="success"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <a href="add_person.php" class="btn-primary">Ongeza Mtu Mpya</a>
    <ul>
        <?php foreach ($persons as $p): ?>
            <li>
                <?= htmlspecialchars($p['full_name']) ?> (<?= htmlspecialchars($p['ward']) ?>)
                - <a href="delete_person.php?id=<?= $p['id'] ?>" onclick="return confirm('Una uhakika unataka kufuta?')">Futa</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
