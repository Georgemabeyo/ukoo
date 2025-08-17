<?php
include 'config.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo "Mzee, hujajisajili bado!";
    exit;
}

// Pata mtu
$res_person = pg_query_params($conn, "SELECT * FROM family_tree WHERE id = $1", [$id]);
if (!$res_person || pg_num_rows($res_person) == 0) {
    echo "Hakuna taarifa za mtu huyu.";
    exit;
}
$person = pg_fetch_assoc($res_person);

// Pata mzazi (parent)
$parent = null;
if ($person['parent_id']) {
    $res_parent = pg_query_params($conn, "SELECT * FROM family_tree WHERE id = $1", [$person['parent_id']]);
    if ($res_parent && pg_num_rows($res_parent)) {
        $parent = pg_fetch_assoc($res_parent);
    }
}

// Pata watoto wa mzazi
$children = [];
if ($parent) {
    $res_children = pg_query_params($conn, "SELECT * FROM family_tree WHERE parent_id = $1 ORDER BY first_name, last_name", [$parent['id']]);
    while ($row = pg_fetch_assoc($res_children)) {
        $children[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<title>Usajili Umefanikiwa</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<?php include 'header.php'; ?>

<div class="container">
  <h2>Hongera, <?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?>!</h2>
  <p>Umefanikiwa kujisajili kwenye Ukoo wa Makomelelo.</p>

  <?php if ($parent): ?>
  <h3>Ukoo upande wa mzazi: <?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></h3>
  <ul class="tree">
    <li>
      <div class="member">
        <p><strong><?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></strong></p>
      </div>
      <ul>
        <?php foreach ($children as $child): ?>
          <li>
            <div class="member"><p><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></p></div>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  </ul>
  <?php else: ?>
    <p>Mzazi haajapatikana.</p>
  <?php endif; ?>

  <p><a href="family_tree.php" class="btn-custom">Angalia Ukoo Wako Kamili</a></p>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
