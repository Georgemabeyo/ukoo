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
  <button class="nav-toggle" aria-label="Toggle navigation">
    <span></span><span></span><span></span>
  </button>
  <nav class="nav-links">
    <a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">Nyumbani</a>
    <a href="family_tree.php" class="<?= ($currentPage === 'family_tree.php') ? 'active' : '' ?>">Ukoo</a>
    <a href="events.php" class="<?= ($currentPage === 'events.php') ? 'active' : '' ?>">Matukio</a>
    <a href="contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?>">Mawasiliano</a>
  </nav>
  <div class="user-status" style="float: right; display: flex; align-items: center; gap: 10px;">
    <?php if ($isLoggedIn): ?>
      <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Picha ya <?= htmlspecialchars($username) ?>" style="width:32px; height:32px; border-radius:50%; object-fit: cover;">
      <span><?= htmlspecialchars($username) ?></span>
      <a href="logout.php" class="btn btn-sm btn-outline-light ms-2">Toka</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-sm btn-outline-light me-2">Ingia</a>
      <a href="registration.php" class="btn btn-sm btn-outline-light">Jisajili</a>
    <?php endif; ?>
    <span id="toggleTheme" style="cursor:pointer; font-weight:700; margin-left:15px;">Dark Mode</span>
  </div>
</header>
