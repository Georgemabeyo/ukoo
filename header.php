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
    <button id="toggleTheme" class="btn btn-sm btn-outline-secondary fw-bold mt-2 mt-md-0 ms-md-3" aria-pressed="false">Dark Mode</button>
  </nav>

  <style>
    /* Default nav visible for desktop */
    nav#navMenu {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    /* Hide nav on mobile by default */
    @media (max-width: 768px) {
      nav#navMenu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        right: 20px;
        background: linear-gradient(180deg, var(--primary, #0d47a1), var(--secondary, #1976d2));
        border-radius: 10px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, box-shadow 0.35s;
        width: 220px;
        box-shadow: none;
        aria-hidden: true;
      }
      nav#navMenu.show {
        display: flex !important;
        max-height: 600px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        aria-hidden: false;
      }
      .nav-toggle {
        display: flex;
      }
    }
    /* Nav toggle button styles */
    .nav-toggle {
      display: none;
      flex-direction: column;
      justify-content: space-between;
      width: 30px;
      height: 24px;
      background: transparent;
      border: none;
      cursor: pointer;
      z-index: 1100;
    }
    .nav-toggle span {
      height: 3px;
      background: var(--primary, #0d47a1);
      border-radius: 2px;
      transition: all 0.4s;
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
    document.addEventListener('DOMContentLoaded', function () {
      const navToggleBtn = document.querySelector('.nav-toggle');
      const navMenu = document.getElementById('navMenu');
      const toggleThemeBtn = document.getElementById('toggleTheme');

      navToggleBtn.addEventListener('click', () => {
        const shown = navMenu.classList.toggle('show');
        navToggleBtn.classList.toggle('active', shown);
        navToggleBtn.setAttribute('aria-expanded', shown ? 'true' : 'false');
        navMenu.setAttribute('aria-hidden', shown ? 'false' : 'true');
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
