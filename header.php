<?php
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
if (!isset($isLoggedIn)) {
    $isLoggedIn = false;
}
if (!isset($username)) {
    $username = '';
}
if (!isset($userPhoto)) {
    $userPhoto = 'default-avatar.png';
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ukoo wa Makomelelo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
  <div class="logo">Ukoo wa Makomelelo</div>
  
  <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu" style="background:none; border:none; cursor:pointer;">
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
  </button>
  
  <nav id="navMenu" class="nav-links" style="display:none; flex-direction: column; gap: 10px; background: #f0f0f0; padding: 10px;">
    <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">Nyumbani</a>
    <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?>">Ukoo</a>
    <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?>">Matukio</a>
    <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?>">Mawasiliano</a>
    <div class="user-status" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
      <?php if ($isLoggedIn): ?>
        <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Picha ya <?= htmlspecialchars($username) ?>" style="width:32px; height:32px; border-radius:50%; object-fit: cover;">
        <span><?= htmlspecialchars($username) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-dark ms-2">Toka</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-dark me-2">Ingia</a>
        <a href="registration.php" class="btn btn-sm btn-outline-dark">Jisajili</a>
      <?php endif; ?>
      <button id="toggleTheme" style="cursor:pointer; font-weight:700; background:none; border:none;">Dark Mode</button>
    </div>
  </nav>
</header>

<script>
const navToggleBtn = document.querySelector('.nav-toggle');
const navMenu = document.getElementById('navMenu');
const toggleThemeBtn = document.getElementById('toggleTheme');

navToggleBtn.addEventListener('click', () => {
  const isVisible = navMenu.style.display === 'flex';
  navMenu.style.display = isVisible ? 'none' : 'flex';
  navToggleBtn.setAttribute('aria-expanded', !isVisible);
  navMenu.setAttribute('aria-hidden', isVisible);
});

toggleThemeBtn.addEventListener('click', () => {
  const body = document.body;
  const isDarkMode = body.classList.toggle('dark-mode');
  toggleThemeBtn.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
  localStorage.setItem('prefers-dark', isDarkMode ? 'yes' : 'no');
});

window.addEventListener('DOMContentLoaded', () => {
  const prefersDark = localStorage.getItem('prefers-dark');
  if (prefersDark === 'yes') {
    document.body.classList.add('dark-mode');
    toggleThemeBtn.textContent = 'Light Mode';
  } else {
    document.body.classList.remove('dark-mode');
    toggleThemeBtn.textContent = 'Dark Mode';
  }
});
</script>
