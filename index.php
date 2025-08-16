<?php // index.php ?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo | Karibu</title>
<style>
/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(120deg, #a0d8ef 0%, #f5f7fa 100%);
    color: #222;
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 15px 40px;
    transition: background 0.3s, color 0.3s;
}

/* Dark mode */
body.dark {
    background: #121212;
    color: #eee;
}
body.dark header,
body.dark nav,
body.dark footer {
    background: #1f1f1f;
    color: #ffc107;
}
body.dark .feature-box {
    background: #1e1e1e;
    color: #eee;
}
body.dark #searchInput {
    background: #1e1e1e;
    color: #fff;
    border-color: #ffc107;
}
body.dark #searchResults {
    background: #1f1f1f;
    border-color: #ffc107;
}
body.dark #personDetails {
    background: #1f1f1f;
    color: #fff;
}

/* Header */
header {
    background: #0d47a1;
    color: #ffc107;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 1200px;
    position: relative;
    z-index: 1000;
    border-radius: 0 0 15px 15px;
}
header .logo {
    font-weight: 700;
    font-size: 1.8rem;
    letter-spacing: 2px;
    user-select: none;
}

/* Nav */
nav {
    position: absolute;
    top: 100%;
    right: 30px;
    background: #0d47a1;
    flex-direction: column;
    width: 220px;
    border-radius: 0 0 10px 10px;
    max-height: 0;
    overflow: hidden;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    transition: max-height 0.35s ease;
    z-index: 1500;
}
nav.open {
    max-height: 350px;
}
nav a {
    display: block;
    padding: 12px 25px;
    color: #ffc107;
    font-weight: 600;
    font-size: 1.1rem;
    text-decoration: none;
    border-radius: 0 0 0 10px;
    transition: background-color 0.3s ease;
}
nav a:hover, nav a:focus {
    background: #ffc107;
    color: #0d47a1;
    outline: none;
}

/* Menu Toggle */
.menu-toggle {
    display: flex;
    flex-direction: column;
    cursor: pointer;
    width: 30px;
    height: 25px;
    justify-content: space-between;
    user-select: none;
    z-index: 1600;
}
.menu-toggle span {
    height: 3px;
    width: 100%;
    background: #ffc107;
    border-radius: 3px;
    transition: all 0.3s ease;
}
.menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}
.menu-toggle.active span:nth-child(2) {
    opacity: 0;
}
.menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* Hero */
.hero {
    height: 70vh;
    width: 100%;
    max-width: 1200px;
    border-radius: 15px;
    display: flex;
    filter: brightness(0.95);
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 20px;
    color: #0d47a1;
    text-shadow: 1px 1px 6px rgba(255,255,255,0.7);
    flex-direction: column;
    user-select: none;
    margin: 30px 0;
}
.hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    font-weight: 900;
    letter-spacing: 1.5px;
}
.hero p {
    font-size: 1.3rem;
    max-width: 600px;
    margin-bottom: 30px;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Button */
.btn-primary {
    background: #20b2aa; /* friendly teal */
    color: #fff;
    padding: 12px 26px;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    user-select: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.btn-primary:hover,
.btn-primary:focus {
    background: #17a398; /* slightly darker */
    outline: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}

/* Main */
main {
    background: white;
    width: 100%;
    max-width: 1200px;
    border-radius: 15px;
    padding: 40px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    flex-grow: 1;
    margin-bottom: 30px;
}
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
    gap: 30px;
}
.feature-box {
    background: #f9f9f9;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    user-select: none;
}
.feature-box:hover,
.feature-box:focus-within {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
.feature-box h3 {
    color: #0d47a1;
    margin-bottom: 15px;
    font-weight: 700;
}
.feature-box p {
    color: #555;
    font-weight: 500;
    font-size: 1rem;
}

/* Footer */
footer {
    background: #0d47a1;
    color: #ffc107;
    text-align: center;
    padding: 20px 10px;
    font-size: 0.9rem;
    border-radius: 0 0 15px 15px;
    width: 100%;
    max-width: 1200px;
    user-select: none;
}

/* Toggle Dark Mode Button */
#darkModeToggle {
    background: #ffc107;
    color: #0d47a1;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}
#darkModeToggle:hover {
    background: #ffb300;
}

/* Responsive nav */
@media(max-width: 768px) {
    nav a { font-size: 1.2rem; }
}
@media(max-width: 600px) {
    header { padding: 15px 20px; }
    .hero { margin: 20px 0; height: 50vh; }
    .hero h1 { font-size: 2.2rem; }
    .hero p { font-size: 1.1rem; }
}
</style>
</head>
<body>

<header>
    <div class="logo" aria-label="Ukoo wa Makomelelo">Ukoo wa Makomelelo</div>
    <button id="darkModeToggle" aria-label="Toggle dark mode">Dark Mode</button>
    <div class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <nav id="nav-menu" role="navigation" aria-label="Main navigation" aria-hidden="true">
        <a href="index.php">Nyumbani</a>
        <a href="registration.php">Jisajiri</a>
        <a href="family_tree.php">Ukoo</a>
        <a href="events.html">Matukio</a>
        <a href="contact.php">Mawasiliano</a>
    </nav>
</header>

<section class="hero" role="banner" aria-label="Hero Section">
    <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
    <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
    <a href="registration.php" class="btn-primary" role="button" aria-label="Jiandikishe Sasa">Jiandikishe Sasa</a>
</section>

<main>
    <section class="features" aria-label="Sehemu za huduma kuu">
        <div class="feature-box" tabindex="0">
            <h3>Usajili Rahisi</h3>
            <p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p>
        </div>
        <div class="feature-box" tabindex="0">
            <h3>Uchunguzi wa Familia</h3>
            <p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p>
        </div>
        <div class="feature-box" tabindex="0">
            <h3>Usalama wa Taarifa</h3>
            <p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p>
        </div>
        <div class="feature-box" tabindex="0">
            <h3>Muonekano wa Kisasa</h3>
            <p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p>
        </div>
    </section>
</main>

<footer>
    &copy; 2025 Ukoo wa Makomelelo | Haki zote zimehifadhiwa
</footer>

<script>
// Toggle Nav
const menuToggle = document.getElementById('menu-toggle');
const navMenu = document.getElementById('nav-menu');
menuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('open');
    menuToggle.classList.toggle('active');
    if(navMenu.classList.contains('open')){
        navMenu.setAttribute('aria-hidden', 'false');
    } else {
        navMenu.setAttribute('aria-hidden', 'true');
    }
});
menuToggle.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        menuToggle.click();
    }
});

// Dark mode toggle
const darkToggle = document.getElementById('darkModeToggle');
darkToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    darkToggle.textContent = document.body.classList.contains('dark') ? 'Light Mode' : 'Dark Mode';
});
</script>
</body>
</html>
