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
  <style>
    /* Additional styling for layout */
    header.header-container {
      position: relative;
    }
    .user-info {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1050;
      background: #f8f9fa;
      padding: 5px 10px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      align-items: center;
    }
    .user-info img {
      width: 40px;
      height: 40px;
      object-fit: cover;
    }
    .user-info span {
      margin-left: 8px;
      font-weight: 600;
      white-space: nowrap;
    }
    /* Adjust nav-toggle button on top right */
    .nav-toggle {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 1060;
      border: none;
      background: transparent;
    }

    /* Adjust nav menu on all devices */
    nav#navMenu {
      margin-left: 100px; /* leave space for user-info */
      padding-left: 0 !important; /* remove padding from bootstrap */
      background: transparent !important;
      box-shadow: none !important;
      position: relative;
      z-index: 1040;
    }

    /* Mobile nav menu - sidebar style */
    @media (max-width: 767.98px) {
      nav#navMenu {
        background: #f8f9fa;
        position: fixed;
        top: 50px;  /* below toggle */
        right: 0;
        height: calc(100vh - 50px);
        width: 240px;
        overflow-y: auto;
        display: none !important;
        flex-direction: column !important;
        padding: 1rem;
        border-radius: 0 0 0 10px;
        box-shadow: -2px 0 8px rgba(0,0,0,0.15);
        margin-left: 0;
      }
      nav#navMenu.d-md-flex {
        display: flex !important;
      }
      .nav-link, .btn {
        margin-bottom: 0.75rem;
      }
    }
    /* Desktop nav menu inline */
    @media (min-width: 768px) {
      nav#navMenu {
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
      }
    }
  </style>
</head>
<body>
  <header class="header-container d-flex align-items-center justify-content-between flex-wrap p-3 bg-light">
    <!-- User info outside nav, fixed top-left -->
    <?php if ($isLoggedIn): ?>
      <div class="user-info d-flex">
        <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Picha ya <?= htmlspecialchars($username) ?>" class="rounded-circle" />
        <span><?= htmlspecialchars($username) ?></span>
      </div>
    <?php endif; ?>

    <div class="logo fw-bold fs-4 order-md-1">Ukoo wa Makomelelo</div>

    <button class="nav-toggle btn btn-light d-md-none" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu" type="button">
      <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
      <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
      <span style="display:block; width:25px; height:3px; background:#000; margin:5px 0;"></span>
    </button>

    <nav id="navMenu" class="nav-links d-none d-md-flex gap-3" aria-hidden="true">
      <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?> nav-link">Nyumbani</a>
      <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?> nav-link">Ukoo</a>
      <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?> nav-link">Matukio</a>
      <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?> nav-link">Mawasiliano</a>
      <?php if ($isLoggedIn): ?>
        <a href="logout.php" class="btn btn-sm btn-outline-danger ms-md-3 mt-2 mt-md-0">Toka</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-primary ms-md-3 mt-2 mt-md-0">Ingia</a>
        <a href="registration.php" class="btn btn-sm btn-primary ms-md-2 mt-2 mt-md-0">Jisajili</a>
      <?php endif; ?>
      <button id="toggleTheme" class="btn btn-sm btn-outline-secondary fw-bold mt-2 mt-md-0 ms-md-3">Dark Mode</button>
    </nav>
  </header>

<script>
  const navToggleBtn = document.querySelector('.nav-toggle');
  const navMenu = document.getElementById('navMenu');
  const toggleThemeBtn = document.getElementById('toggleTheme');

  navToggleBtn.addEventListener('click', () => {
    const isHidden = navMenu.classList.contains('d-none');
    if (isHidden) {
      navMenu.classList.remove('d-none');
      navToggleBtn.setAttribute('aria-expanded', 'true');
      navMenu.setAttribute('aria-hidden', 'false');
    } else {
      navMenu.classList.add('d-none');
      navToggleBtn.setAttribute('aria-expanded', 'false');
      navMenu.setAttribute('aria-hidden', 'true');
    }
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
</body>
</html>
