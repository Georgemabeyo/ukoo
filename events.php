<?php
session_start();

$file = 'events.json';
$events = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $events = json_decode($json, true);
    if (!is_array($events)) $events = [];
}

$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Matukio | Ukoo wa Makomelelo</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
<?php include 'header.php'; ?>

<section class="events-container container">
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <article class="event-card">
                <?php if (!empty($event['photo'])): ?>
                  <img src="uploads/<?= htmlspecialchars($event['photo']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image" />
                <?php else: ?>
                  <img src="https://via.placeholder.com/600x400?text=No+Image" alt="Hakuna Picha" class="event-image" />
                <?php endif; ?>
                <div class="event-content">
                    <h2 class="event-title"><?= htmlspecialchars($event['title']) ?></h2>
                    <time datetime="<?= htmlspecialchars($event['date'] ?? '') ?>" class="event-date">
                        <?= !empty($event['date']) ? date('F j, Y', strtotime($event['date'])) : 'Tarehe haijajulikana' ?>
                    </time>
                    <p class="event-description"><?= nl2br(htmlspecialchars($event['desc'])) ?></p>
                    <a href="<?= htmlspecialchars($event['read_more_link'] ?? '#') ?>" class="btn-readmore">Soma Zaidi</a>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Hakuna matukio yaliyopo kwa sasa.</p>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
<script src="scripts.js"></script>
</body>
</html>
