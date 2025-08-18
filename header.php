<?php
// Hakikisha $currentPage na $isLoggedIn wameanzishwa kabla ya include hii

if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}

if (!isset($isLoggedIn)) {
    $isLoggedIn = false; // Default false kama haijatolewa
}
?>

<header>
  <div class="logo">Ukoo wa Makomelelo</div>
  <button class="nav-toggle" aria-label="Toggle navigation">
    <span></span><span></span><span></span>
  </button>
  <nav class="nav-links">
    <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">Nyumbani</a>
    <a href="registration.php" class="<?= ($currentPage === 'registration.php') ? 'active' : '' ?>">Jisajiri</a>
    <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?>">Ukoo</a>
    <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?>">Matukio</a>
    <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?>">Mawasiliano</a>

    <?php if ($isLoggedIn): ?>
      <a href="logout.php">Toka</a>
    <?php else: ?>
      <a href="login.php">Ingia</a>
    <?php endif; ?>
    <span id="toggleTheme" style="cursor:pointer; font-weight:700;">Dark Mode</span>
  </nav>
</header>
