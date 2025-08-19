<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$searchResults = [];
$searchQuery = '';

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchQuery = trim($_GET['search']);
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div class="logo">Ukoo wa Makomelelo</div>
  <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu">
    <span></span><span></span><span></span>
  </button>
  <nav id="navMenu" class="nav-links" aria-hidden="true">
    <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">Nyumbani</a>
    <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?>">Ukoo</a>
    <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?>">Matukio</a>
    <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?>">Mawasiliano</a>

    <div class="user-status">
      <?php if ($isLoggedIn): ?>
        <img src="<?= htmlspecialchars($userPhoto ?? 'default-avatar.png') ?>" alt="Picha ya <?= htmlspecialchars($username) ?>">
        <span><?= htmlspecialchars($username) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-dark ms-2">Toka</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-dark me-2">Ingia</a>
        <a href="registration.php" class="btn btn-sm btn-outline-dark">Jisajili</a>
      <?php endif; ?>
      <button id="toggleTheme" class="btn btn-sm btn-outline-dark ms-2" style="font-weight:700; cursor:pointer;">Dark Mode</button>
    </div>
  </nav>
</header>

<section class="hero container py-5 text-center">
  <?php if ($isLoggedIn): ?>
    <div class="mb-4"><strong>Karibu, <?= htmlspecialchars($username) ?></strong></div>
  <?php endif; ?>
  <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
  <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
  <form method="GET" action="index.php" class="mx-auto d-flex" style="max-width:400px;">
    <input type="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Tafuta familia..." 
           class="form-control rounded-0 rounded-start border-warning" style="font-size:1rem; height:40px;">
    <button type="submit" class="btn btn-warning rounded-0 rounded-end" style="font-size:1rem; height:40px;">Tafuta</button>
  </form>
</section>

<?php if ($searchQuery !== ''): ?>
<div class="container my-4">
  <h2>Matokeo ya Utafutaji kwa: <?= htmlspecialchars($searchQuery) ?></h2>
  <?php if (count($searchResults) > 0): ?>
    <ul class="list-unstyled">
      <?php foreach($searchResults as $person): ?>
      <li class="d-flex align-items-center mb-3">
        <?php 
          $imgSrc = !empty($person['photo']) ? 'uploads/'.htmlspecialchars($person['photo']) : 'https://via.placeholder.com/50?text=No+Image'; 
        ?>
        <img src="<?= $imgSrc ?>" alt="Picha ya <?= htmlspecialchars($person['first_name'].' '.$person['last_name']) ?>" 
             class="rounded-circle me-3" style="width:50px; height:50px; object-fit:cover; border:2px solid #ffc107;">
        <a href="family_tree.php?member=<?= (int)$person['id'] ?>" class="fw-bold text-primary">
          <?= htmlspecialchars($person['first_name'].' '.$person['last_name']) ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Hakuna matokeo yaliyo patikana kwa utafutaji huu.</p>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="features container py-5">
  <div class="row g-4 text-center">
    <div class="col-md-3 feature-box">
      <h3>Usajili Rahisi</h3>
      <p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p>
    </div>
    <div class="col-md-3 feature-box">
      <h3>Uchunguzi wa Familia</h3>
      <p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p>
    </div>
    <div class="col-md-3 feature-box">
      <h3>Usalama wa Taarifa</h3>
      <p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p>
    </div>
    <div class="col-md-3 feature-box">
      <h3>Muonekano wa Kisasa</h3>
      <p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="jquery-3.6.0.min.js"></script>
<script src="scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const navToggleBtn = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.nav-links');
  const toggleThemeBtn = document.getElementById('toggleTheme');

  navToggleBtn.addEventListener('click', () => {
    if (navLinks.style.display === 'flex') {
      navLinks.style.display = 'none';
      navToggleBtn.setAttribute('aria-expanded', 'false');
      navLinks.setAttribute('aria-hidden', 'true');
    } else {
      navLinks.style.display = 'flex';
      navToggleBtn.setAttribute('aria-expanded', 'true');
      navLinks.setAttribute('aria-hidden', 'false');
    }
  });

  toggleThemeBtn.addEventListener('click', () => {
    const body = document.body;
    const darkModeActive = body.classList.toggle('dark-mode');
    toggleThemeBtn.textContent = darkModeActive ? 'Light Mode' : 'Dark Mode';
    localStorage.setItem('prefers-dark', darkModeActive ? 'yes' : 'no');
  });

  window.addEventListener('DOMContentLoaded', () => {
    const prefersDark = localStorage.getItem('prefers-dark');
    if (prefersDark === 'yes') {
      document.body.classList.add('dark-mode');
      toggleThemeBtn.textContent = 'Light Mode';
    }
    else {
      document.body.classList.remove('dark-mode');
      toggleThemeBtn.textContent = 'Dark Mode';
    }
  });
</script>
</body>
</html>
