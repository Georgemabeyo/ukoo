<?php

include 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);

$searchResults = [];
$searchQuery = '';
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchQuery = trim($_GET['search']);
    // Use prepared statement to prevent SQL injection
    $sql = "SELECT first_name, last_name, id, photo FROM family_tree WHERE first_name ILIKE $1 OR last_name ILIKE $1 LIMIT 20";
    $param = ['%' . $searchQuery . '%'];
    $res = pg_query_params($conn, $sql, $param);
    if ($res && pg_num_rows($res) > 0) {
        $searchResults = pg_fetch_all($res);
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ukoo wa Makomelelo | Karibu</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">

<?php include 'header.php'; ?>

<section class="hero container">
  <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
  <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
  
  <form method="GET" action="index.php" style="margin:20px auto; max-width:400px; display:flex;">
  <input type="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Tafuta familia..." 
         style="flex-grow:1; padding:10px; border-radius:8px 0 0 8px; border:2px solid #ffc107; font-size:1rem; height:40px;">
  <button type="submit" class="btn-primary" style="border-radius:0 8px 8px 0; flex-shrink:0; padding:0 20px; font-size:1rem; height:40px;">Tafuta</button>
</form>

  <a href="registration.php" class="btn-primary">Jiandikishe Sasa</a>
</section>

<?php if ($searchQuery !== ''): ?>
<div class="container" style="margin-top:30px;">
  <h2>Matokeo ya Utafutaji kwa: <?= htmlspecialchars($searchQuery) ?></h2>
  <?php if (count($searchResults) > 0): ?>
    <ul style="list-style:none; padding:0;">
      <?php foreach($searchResults as $person): ?>
      <li style="margin-bottom:10px; display:flex; align-items:center;">
        <?php $imgSrc = !empty($person['photo']) ? 'uploads/'.htmlspecialchars($person['photo']) : 'https://via.placeholder.com/50?text=No+Image'; ?>
        <img src="<?= $imgSrc ?>" alt="Picha ya <?= htmlspecialchars($person['first_name'].' '.$person['last_name']) ?>" style="width:50px; height:50px; border-radius:50%; margin-right:10px; object-fit:cover; border:2px solid #ffc107;">
        <a href="family_tree.php?member=<?= (int)$person['id'] ?>" style="color:#0d47a1; font-weight:700;"><?= htmlspecialchars($person['first_name'].' '.$person['last_name']) ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Hakuna matokeo yaliyo patikana kwa utafutaji huu.</p>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="features container">
  <div class="feature-box">
    <h3>Usajili Rahisi</h3>
    <p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p>
  </div>
  <div class="feature-box">
    <h3>Uchunguzi wa Familia</h3>
    <p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p>
  </div>
  <div class="feature-box">
    <h3>Usalama wa Taarifa</h3>
    <p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p>
  </div>
  <div class="feature-box">
    <h3>Muonekano wa Kisasa</h3>
    <p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="jquery-3.6.0.min.js"></script>
<script src="scripts.js"></script>
</body>
</html>
