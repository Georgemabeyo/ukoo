<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id) {
  echo "<em>Mzazi hayupo au haujajulikana.</em>";
  exit;
}

// Pata mzazi
$res_parent = pg_query_params($conn, "SELECT * FROM family_tree WHERE id=$1", [$id]);
if(!$res_parent || pg_num_rows($res_parent) == 0) {
  echo "<em>Mzazi haajapatikana.</em>";
  exit;
}
$parent = pg_fetch_assoc($res_parent);

// Pata uzao (watoto wa mzazi)
$res_children = pg_query_params($conn, "SELECT * FROM family_tree WHERE parent_id=$1 ORDER BY first_name, last_name", [$parent['id']]);
$children = [];
while($row = pg_fetch_assoc($res_children)) {
  $children[] = $row;
}

// Onyesha mzazi na uzao wake
?>
<div class="parent-info">
  <strong>Mzazi: <?=htmlspecialchars($parent['first_name'].' '.$parent['last_name'])?></strong>
  <?php if(count($children) > 0): ?>
  <ul>
    <?php foreach($children as $child): ?>
      <li><?=htmlspecialchars($child['first_name'].' '.$child['last_name'])?></li>
    <?php endforeach; ?>
  </ul>
  <?php else: ?>
    <em>Hamna uzao wa mzazi hapo.</em>
  <?php endif; ?>
</div>
