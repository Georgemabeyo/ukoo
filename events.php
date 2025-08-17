<?php
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
    <article class="event-card">
        <img src="https://images.unsplash.com/photo-1468071174046-657d9d351a40?auto=format&fit=crop&w=600&q=80" alt="Mkutano Mkuu" class="event-image" />
        <div class="event-content">
            <h2 class="event-title">Mkutano Mkuu wa Ukoo January 2026</h2>
            <time datetime="2026-01-15" class="event-date">January 15, 2026</time>
            <p class="event-description">Tukio hili linaangazia umuhimu wa kujua asili na kuendeleza urithi wa ukoo.</p>
            <a href="#" class="btn-readmore">Soma Zaidi</a>
        </div>
    </article>
    <article class="event-card">
        <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=600&q=80" alt="Michango" class="event-image" />
        <div class="event-content">
            <h2 class="event-title">Michango Mbalimbali ya Wanaukoo</h2>
            <time datetime="2025-08-14" class="event-date">Endelevu (Inatokea)</time>
            <p class="event-description">Wanaukoo wote mnaombwa kujitahidi na kuhamasika zaidi katika michango mbalimbali.</p>
            <a href="#" class="btn-readmore">Soma Zaidi</a>
        </div>
    </article>
    <article class="event-card">
        <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=600&q=80" alt="Mkutano Mkuu Familia" class="event-image" />
        <div class="event-content">
            <h2 class="event-title">Mkutano Mkuu wa Familia 2025</h2>
            <time datetime="2025-01-02" class="event-date">January 1, 2025</time>
            <p class="event-description">Familia ya Makomelelo ilikusanyika kusherehekea urithi na historia yao.</p>
            <a href="#" class="btn-readmore">Soma Zaidi</a>
        </div>
    </article>
</section>

<?php include 'footer.php'; ?>

<script src="scripts.js"></script>
</body>
</html>
