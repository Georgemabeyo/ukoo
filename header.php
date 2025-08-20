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
<header class="header-container">
  <?php if ($isLoggedIn): ?>
    <div class="user-info" title="<?= htmlspecialchars($username) ?>" aria-label="User info">
      <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Picha ya <?= htmlspecialchars($username) ?>" />
      <span><?= htmlspecialchars($username) ?></span>
    </div>
  <?php endif; ?>

  <div class="logo" role="heading" aria-level="1" tabindex="0">Ukoo wa Makomelelo</div>

  <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu" type="button" aria-pressed="false">
    <span></span>
    <span></span>
    <span></span>
  </button>

  <nav id="navMenu" class="nav-links" aria-label="Main navigation" aria-hidden="false">
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
    <button id="toggleTheme" class="btn btn-sm btn-outline-secondary fw-bold mt-2 mt-md-0 ms-md-3">Dark Mode</button>
  </nav>

  <style>
    /* Nav always visible on desktop */
    nav#navMenu {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    /* Hide nav on mobile by default */
    @media (max-width: 768px) {
      nav#navMenu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 55px;
        right: 15px;
        background: linear-gradient(180deg, #0d47a1, #1976d2);
        border-radius: 10px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, box-shadow 0.3s ease;
        width: 220px;
        box-shadow: none;
        aria-hidden: true;
        padding: 10px 10px;
        z-index: 1050;
      }
      nav#navMenu.show {
        display: flex !important;
        max-height: 600px;
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.35);
        aria-hidden: false;
      }
      .nav-toggle {
        display: flex;
      }
    }
    /* Nav toggle button */
    .nav-toggle {
      display: none;
      flex-direction: column;
      justify-content: space-between;
      width: 30px;
      height: 24px;
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 0;
      z-index: 1100;
    }
    .nav-toggle span {
      display: block;
      height: 3px;
      background: #0d47a1;
      border-radius: 2px;
      transition: all 0.3s ease;
    }
    .nav-toggle.active span:nth-child(1) {
      transform: rotate(45deg) translate(5px, 5px);
    }
    .nav-toggle.active span:nth-child(2) {
      opacity: 0;
    }
    .nav-toggle.active span:nth-child(3) {
      transform: rotate(-45deg) translate(5px, -5px);
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const navToggleBtn = document.querySelector('.nav-toggle');
      const navMenu = document.getElementById('navMenu');
      const toggleThemeBtn = document.getElementById('toggleTheme');

      navToggleBtn.addEventListener('click', () => {
        navMenu.classList.toggle('show');
        const isShown = navMenu.classList.contains('show');
        navToggleBtn.classList.toggle('active', isShown);
        navToggleBtn.setAttribute('aria-expanded', isShown.toString());
        navMenu.setAttribute('aria-hidden', (!isShown).toString());
      });

      toggleThemeBtn.addEventListener('click', () => {
        const body = document.body;
        const isDarkMode = body.classList.toggle('dark-mode');
        toggleThemeBtn.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
        toggleThemeBtn.setAttribute('aria-pressed', isDarkMode.toString());
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
