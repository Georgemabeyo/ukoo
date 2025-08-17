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
<style>
/* Global */
body, html { margin:0; padding:0; font-family:'Segoe UI', sans-serif; transition: background 0.3s,color 0.3s;}
a { text-decoration:none; }

/* Light/Dark Mode */
body.light-mode { background:#f0f4f8; color:#222; }
body.dark-mode { background:#1e293b; color:#f8fafc; }

/* Header */
header {
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 25px; border-radius:0 0 15px 15px;
    background:linear-gradient(90deg,#0d47a1,#1976d2);
    position:relative; z-index:1000;
}
.logo { font-size:1.8rem; font-weight:700; color:#ffc107; }
.nav-links { display:flex; gap:20px; align-items:center; }
.nav-links a { color:#ffc107; font-weight:600; padding:8px 12px; border-radius:6px; transition:0.3s; }
.nav-links a:hover { background:#ffc107; color:#0d47a1; }

/* Toggle button */
.nav-toggle {
    display:none; flex-direction:column; justify-content:space-between;
    width:30px; height:24px; background:transparent; border:none; cursor:pointer;
}
.nav-toggle span {
    display:block; height:3px; background:#ffc107; border-radius:2px; transition:all 0.4s;
}
.nav-toggle.active span:nth-child(1){ transform:rotate(45deg) translate(5px,5px);}
.nav-toggle.active span:nth-child(2){ opacity:0; }
.nav-toggle.active span:nth-child(3){ transform:rotate(-45deg) translate(5px,-5px); }

/* Hero */
.hero { text-align:center; margin:20px auto; padding:60px 20px; border-radius:15px;
    background: linear-gradient(120deg,#2563eb,#3b82f6); color:#fff; box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
.hero h1 { font-size:2.8rem; font-weight:900; margin-bottom:20px; }
.hero p { font-size:1.2rem; margin-bottom:30px; }
.btn-primary { background:#facc15; color:#1e3a8a; padding:12px 26px; font-weight:700; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.2); transition:all 0.3s;}
.btn-primary:hover { background:#eab308; color:#1e3a8a; }

/* Features */
.features { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin:40px 0; padding:0 10px; }
.feature-box { background:#fff; border-radius:15px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:0.3s; }
.feature-box:hover { transform:translateY(-8px); box-shadow:0 10px 20px rgba(0,0,0,0.15); }
body.dark-mode .feature-box { background:#334155; color:#f8fafc; }

/* Footer */
footer { text-align:center; padding:20px; border-radius:15px; background:#2563eb; color:#facc15; margin-top:50px; }

/* Responsive */
@media(max-width:768px){
    .hero h1{ font-size:2rem;} .hero p{ font-size:1rem; }
    .nav-links { flex-direction:column; position:absolute; top:100%; right:20px; background:linear-gradient(180deg,#0d47a1,#1976d2);
        border-radius:10px; overflow:hidden; max-height:0; transition:max-height 0.35s ease, box-shadow 0.35s;
    }
    .nav-links.show { max-height:500px; box-shadow:0 8px 16px rgba(0,0,0,0.3);}
    .nav-toggle { display:flex;}
}
</style>
</head>
<body class="light-mode">
<header>
    <div class="logo">Ukoo wa Makomelelo</div>
    <button class="nav-toggle" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
    </button>
    <nav class="nav-links">
        <a href="index.php">Nyumbani</a>
        <a href="registration.php">Jisajiri</a>
        <a href="family_tree.php">Ukoo</a>
        <a href="events.php">Matukio</a>
        <a href="contact.php">Mawasiliano</a>
        <?php if($isLoggedIn): ?>
        <a href="logout.php">Toka</a>
        <?php else: ?>
        <a href="login.php">Ingia</a>
        <?php endif; ?>
        <span id="toggleTheme" style="cursor:pointer; font-weight:700;">Dark Mode</span>
    </nav>
</header>

<section class="hero">
    <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
    <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
    <a href="registration.php" class="btn-primary">Jiandikishe Sasa</a>
</section>

<div class="features">
    <div class="feature-box"><h3>Usajili Rahisi</h3><p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p></div>
    <div class="feature-box"><h3>Uchunguzi wa Familia</h3><p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p></div>
    <div class="feature-box"><h3>Usalama wa Taarifa</h3><p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p></div>
    <div class="feature-box"><h3>Muonekano wa Kisasa</h3><p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p></div>
</div>

<footer>
    &copy; 2025 Ukoo wa Makomelelo | Haki zote zimehifadhiwa
</footer>

<script>
// Set mode from localStorage on page load
document.addEventListener('DOMContentLoaded', ()=>{
    const savedMode = localStorage.getItem('theme');
    const themeToggle = document.getElementById('toggleTheme');
    if(savedMode==='dark'){
        document.body.classList.replace('light-mode','dark-mode');
        themeToggle.textContent='Light Mode';
    }else{
        document.body.classList.replace('dark-mode','light-mode');
        themeToggle.textContent='Dark Mode';
    }
});

// Navbar toggle
const toggleBtn = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
toggleBtn.addEventListener('click', ()=>{
    toggleBtn.classList.toggle('active');
    navLinks.classList.toggle('show');
});

// Theme toggle with saving in localStorage
const themeToggle = document.getElementById('toggleTheme');
themeToggle.addEventListener('click', ()=>{
    if(document.body.classList.contains('light-mode')){
        document.body.classList.replace('light-mode','dark-mode');
        localStorage.setItem('theme','dark');
        themeToggle.textContent='Light Mode';
    }else{
        document.body.classList.replace('dark-mode','light-mode');
        localStorage.setItem('theme','light');
        themeToggle.textContent='Dark Mode';
    }
});
</script>
</body>
</html>
