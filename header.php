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
<header class="header-container" role="banner">
  <!-- User info fixed top-left -->
  <?php if ($isLoggedIn): ?>
    <div class="user-info" title="<?= htmlspecialchars($username) ?>" aria-label="User info">
      <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Picha ya <?= htmlspecialchars($username) ?>" />
      <span><?= htmlspecialchars($username) ?></span>
    </div>
  <?php endif; ?>
  <div class="logo" role="heading" aria-level="1" tabindex="0">Ukoo wa Makomelelo</div>
  <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu" type="button">
    <span></span>
    <span></span>
    <span></span>
  </button>
  <nav id="navMenu" class="nav-links d-none" aria-label="Main navigation" aria-hidden="true">
    <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?> nav-link">Nyumbani</a>
    <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?> nav-link">Ukoo</a>
    <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?> nav-link">Matukio</a>
    <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?> nav-link">Mawasiliano</a>
    <?php if ($isLoggedIn): ?>
      <a href="logout.php" class="btn btn-sm btn-outline-danger ms-md-3 mt-2 mt-md-0">Toka</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-sm btn-outline-primary ms-md-3 mt-2 mt-md-0">Ingia</a>
      <a href="registration.php" class="btn btn-sm btn-outline-primary ms-md-3 mt-2 mt-md-0">Jisajiri</a>
    <?php endif; ?>
    <button id="toggleTheme" class="btn btn-sm btn-outline-secondary fw-bold mt-2 mt-md-0 ms-md-3" aria-pressed="false">Dark Mode</button>
  </nav>

  <style>
    .nav-links.d-none {
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .nav-links.show {
      display: flex !important;
      opacity: 1 !important;
    }
    .nav-links {
      flex-wrap: wrap;
      gap: 10px;
    }
    .nav-toggle.active span {
      background-color: #ffc107;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const navToggleBtn = document.querySelector('.nav-toggle');
      const navMenu = document.getElementById('navMenu');
      const toggleThemeBtn = document.getElementById('toggleTheme');

      navToggleBtn.addEventListener('click', () => {
        const isHidden = navMenu.classList.toggle('d-none');
        if (!isHidden) {
          navMenu.classList.add('show');
          navToggleBtn.classList.add('active');
          navToggleBtn.setAttribute('aria-expanded', 'true');
          navMenu.setAttribute('aria-hidden', 'false');
        } else {
          navMenu.classList.remove('show');
          navToggleBtn.classList.remove('active');
          navToggleBtn.setAttribute('aria-expanded', 'false');
          navMenu.setAttribute('aria-hidden', 'true');
        }
      });

      toggleThemeBtn.addEventListener('click', () => {
        const body = document.body;
        const isDarkMode = body.classList.toggle('dark-mode');
        toggleThemeBtn.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
        toggleThemeBtn.setAttribute('aria-pressed', isDarkMode);
        localStorage.setItem('prefers-dark', isDarkMode ? 'yes' : 'no');
      });

      const prefersDark = localStorage.getItem('prefers-dark');
      if (prefersDark === 'yes') {
        document.body.classList.add('dark-mode');
        toggleThemeBtn.textContent = 'Light Mode';
        toggleThemeBtn.setAttribute('aria-pressed', 'true');
      } else {
        document.body.classList.remove('dark-mode');
        toggleThemeBtn.textContent = 'Dark Mode';
        toggleThemeBtn.setAttribute('aria-pressed', 'false');
      }
    });
  </script>
</header>
