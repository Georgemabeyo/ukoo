<?php
// Hakikisha $currentPage na $isLoggedIn wameanzishwa kabla ya include hii
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
if (!isset($isLoggedIn)) {
    $isLoggedIn = false; // Default false kama haijatolewa
}
if (!isset($username)) {
    $username = ''; // Default bila jina
}
if (!isset($userPhoto)) {
    $userPhoto = 'default-avatar.png'; // Picha ya default kama mtumiaji hana picha
}
?>
<header>
  <div class="logo">Ukoo wa Makomelelo</div>

  <button class="nav-toggle" aria-label="Toggle navigation" style="background:none; border:none; cursor:pointer;">
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
    <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
  </button>

  <nav class="nav-links" style="display:none; flex-direction: column; gap: 10px; background: #f0f0f0; padding: 10px;">
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
// Navigation toggle
const navToggleBtn = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

navToggleBtn.addEventListener('click', () => {
  if (navLinks.style.display === 'flex') {
    navLinks.style.display = 'none';
  } else {
    navLinks.style.display = 'flex';
  }
});

// Dark mode toggle
const toggleThemeBtn = document.getElementById('toggleTheme');
toggleThemeBtn.addEventListener('click', () => {
  const body = document.body;
  const isDark = body.classList.toggle('dark-mode');
  toggleThemeBtn.textContent = isDark ? 'Light Mode' : 'Dark Mode';

  // Optional: Save preference in localStorage
  localStorage.setItem('prefers-dark', isDark ? 'yes' : 'no');
});

// Onload: set theme based on localStorage
window.addEventListener('DOMContentLoaded', () => {
  const prefersDark = localStorage.getItem('prefers-dark');
  if (prefersDark === 'yes') {
    document.body.classList.add('dark-mode');
    toggleThemeBtn.textContent = 'Light Mode';
  }
});
</script>

<style>
/* Basic styling */
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background: #eee;
  position: relative;
  flex-wrap: wrap;
}
.logo {
  font-weight: bold;
  font-size: 1.5rem;
}

/* Navigation links for larger screens */
@media(min-width: 768px) {
  .nav-toggle {
    display: none;
  }
  .nav-links {
    display: flex !important;
    flex-direction: row !important;
    gap: 20px;
    background: none !important;
    padding: 0 !important;
  }
  .user-status {
    margin-top: 0 !important;
  }
}

/* Active link style */
.nav-links a.active {
  font-weight: 700;
  text-decoration: underline;
}

/* Dark mode styles */
body.dark-mode {
  background-color: #121212;
  color: #eee;
}
body.dark-mode header {
  background-color: #1e1e1e;
}
body.dark-mode .nav-links {
  background-color: #232323 !important;
}
body.dark-mode .btn-outline-dark {
  color: #eee;
  border-color: #eee;
}
body.dark-mode .btn-outline-dark:hover {
  background-color: #444;
  border-color: #eee;
}
body.dark-mode a {
  color: #90caf9;
}
</style>
