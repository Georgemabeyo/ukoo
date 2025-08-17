<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ukoo wa Makomelelo | Karibu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Global resets */
body, html {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: background 0.3s, color 0.3s;
}

/* Light / Dark mode classes */
body.light-mode {
    background: #f0f4f8;
    color: #222;
}
body.dark-mode {
    background: #1e293b;
    color: #f8fafc;
}

/* Navbar */
.navbar {
    padding: 1rem 2rem;
    border-radius: 12px;
}
.navbar .navbar-brand {
    font-weight: 700;
    font-size: 1.8rem;
    color: #facc15;
    transition: color 0.3s;
}
.navbar.light-mode .navbar-brand { color: #1e3a8a; }
.navbar-dark-mode .navbar-brand { color: #facc15; }

.navbar-nav .nav-link {
    font-weight: 600;
    transition: all 0.3s;
}
.navbar-nav .nav-link:hover {
    color: #facc15 !important;
}
.navbar.light-mode .nav-link { color: #1e3a8a !important; }
.navbar.dark-mode .nav-link { color: #facc15 !important; }

.navbar-toggler {
    border: none;
}
.navbar-toggler-icon {
    background-color: #facc15;
    width: 30px;
    height: 3px;
    display: block;
    position: relative;
}
.navbar-toggler-icon::before,
.navbar-toggler-icon::after {
    content: '';
    background-color: #facc15;
    width: 30px;
    height: 3px;
    position: absolute;
    left: 0;
    transition: 0.3s;
}
.navbar-toggler-icon::before { top: -10px; }
.navbar-toggler-icon::after { top: 10px; }

/* Hero Section */
.hero {
    background: linear-gradient(120deg,#2563eb,#3b82f6);
    color: #fff;
    border-radius: 15px;
    padding: 60px 20px;
    text-align: center;
    margin-top: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.hero h1 {
    font-size: 2.8rem;
    font-weight: 900;
}
.hero p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}
.btn-primary {
    background-color: #facc15;
    color: #1e3a8a;
    border-radius: 10px;
    font-weight: 700;
    padding: 12px 26px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: all 0.3s;
}
.btn-primary:hover {
    background-color: #eab308;
    color: #1e3a8a;
}

/* Features section */
.features {
    margin-top: 40px;
}
.feature-box {
    background-color: #fff;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.feature-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
body.dark-mode .feature-box {
    background-color: #334155;
    color: #f8fafc;
}

/* Search bar */
#searchInput {
    width: 100%;
    padding: 12px 20px;
    border-radius: 10px;
    border: 2px solid #1e3a8a;
    outline: none;
    margin-bottom: 10px;
    transition: all 0.3s;
}
#searchInput:focus {
    border-color: #facc15;
    box-shadow: 0 0 10px #facc15;
}
#searchResults {
    position: absolute;
    background-color: #fff;
    border-radius: 0 0 12px 12px;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    z-index: 2000;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}
#searchResults .result-item {
    padding: 10px 15px;
    cursor: pointer;
    transition: all 0.25s;
}
#searchResults .result-item:hover {
    background-color: #facc15;
    color: #1e3a8a;
}
body.dark-mode #searchResults {
    background-color: #475569;
}
body.dark-mode #searchResults .result-item:hover {
    background-color: #facc15;
    color: #1e3a8a;
}

/* Person Details */
#personDetails {
    display: none;
    margin-top: 20px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
#personDetails img {
    float: left;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin-right: 20px;
    object-fit: cover;
}

/* Footer */
footer {
    text-align: center;
    padding: 20px 10px;
    border-radius: 15px;
    background-color: #2563eb;
    color: #facc15;
    margin-top: 50px;
}

/* Responsive */
@media(max-width: 768px){
    .hero h1 { font-size: 2rem; }
    .hero p { font-size: 1rem; }
}
</style>
</head>
<body class="light-mode">

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container">
    <a class="navbar-brand" href="#">Ukoo wa Makomelelo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Nyumbani</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Jisajiri</a></li>
        <li class="nav-item"><a class="nav-link" href="family_tree.php">Ukoo</a></li>
        <li class="nav-item"><a class="nav-link" href="events.html">Matukio</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Mawasiliano</a></li>
        <?php if($isLoggedIn): ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Toka</a></li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Ingia</a></li>
        <?php endif; ?>
        <li class="nav-item"><span class="nav-link" id="toggleTheme">🌓</span></li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero">
    <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
    <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
    <a href="registration.php" class="btn btn-primary">Jiandikishe Sasa</a>
</section>

<div class="container mt-4 position-relative">
    <input type="text" id="searchInput" placeholder="Tafuta mtu kwa jina...">
    <div id="searchResults"></div>
    <div id="personDetails"></div>
</div>

<div class="container features">
    <div class="row g-4">
        <div class="col-md-3"><div class="feature-box"><h3>Usajili Rahisi</h3><p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p></div></div>
        <div class="col-md-3"><div class="feature-box"><h3>Uchunguzi wa Familia</h3><p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p></div></div>
        <div class="col-md-3"><div class="feature-box"><h3>Usalama wa Taarifa</h3><p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p></div></div>
        <div class="col-md-3"><div class="feature-box"><h3>Muonekano wa Kisasa</h3><p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p></div></div>
    </div>
</div>

<footer>
    &copy; 2025 Ukoo wa Makomelelo | Haki zote zimehifadhiwa
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggleTheme = document.getElementById('toggleTheme');
toggleTheme.addEventListener('click', ()=>{
    document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('light-mode');
});

const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const personDetails = document.getElementById('personDetails');
let results = [];

searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();
    personDetails.style.display = 'none';
    searchResults.innerHTML = '';
    if(query.length < 2){
        searchResults.style.display = 'none';
        return;
    }
    fetch('search.php?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
        results = data;
        if(results.length === 0){
            searchResults.innerHTML = '<div class="result-item">Hakuna mtu aliye patikana</div>';
            searchResults.style.display = 'block';
            return;
        }
        results.forEach((person)=>{
            const div = document.createElement('div');
            div.classList.add('result-item');
            div.textContent = person.full_name;
            div.addEventListener('click', ()=>{
                personDetails.style.display = 'block';
                personDetails.innerHTML = `
                    <img src="${person.photo_url || 'default-avatar.png'}" alt="${person.full_name}">
                    <h2>${person.full_name}</h2>
                    <p><strong>Umri:</strong> ${person.age || 'Haijulikani'}</p>
                    <p><strong>Mkoa:</strong> ${person.region || 'Haijulikani'}</p>
                    <p><strong>Kijiji/Mji:</strong> ${person.village || 'Haijulikani'}</p>
                    <p><strong>Simu:</strong> ${person.phone || 'Haijulikani'}</p>
                    <p><strong>Barua pepe:</strong> ${person.email || 'Haijulikani'}</p>
                    <p><strong>Hali ya ndoa:</strong> ${person.marital_status || 'Haijulikani'}</p>
                    <p><strong>Watoto:</strong> ${person.children || 'Haijulikani'}</p>
                `;
                searchResults.style.display = 'none';
                searchInput.value = person.full_name;
            });
            searchResults.appendChild(div);
        });
        searchResults.style.display = 'block';
    }).catch(()=>{
        searchResults.innerHTML = '<div class="result-item">Tatizo la mtandao. Jaribu tena.</div>';
        searchResults.style.display = 'block';
    });
});
</script>
</body>
</html>
