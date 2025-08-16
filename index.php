<?php
// index.php
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo | Karibu</title>
<style>
/* Reset */
* { margin:0; padding:0; box-sizing:border-box; }

/* Base Colors */
:root {
    --primary-color: #1976d2;    /* New blue */
    --secondary-color: #ff9800;  /* orange accent */
    --accent-color: #4caf50;     /* green */
    --bg-color: #f0f4f8;         /* light background */
    --text-color: #222;
    --feature-bg: #fff;
}

/* Dark Mode */
body.dark {
    --bg-color: #121212;
    --text-color: #eee;
    --primary-color: #2196f3;
    --secondary-color: #ffc107;
    --accent-color: #00e676;
    --feature-bg: #1e1e1e;
}

/* Body */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
    line-height:1.6;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:0 15px 40px;
    transition: all 0.3s ease;
}

/* Header */
header {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 15px 30px;
    display:flex; justify-content:space-between; align-items:center;
    width:100%; max-width:1200px;
    position: relative;
    z-index: 1000;
    border-radius: 0 0 15px 15px;
}
header .logo { font-weight:700; font-size:1.8rem; letter-spacing:2px; user-select:none; }

/* Nav */
nav {
    position:absolute; top:100%; right:30px;
    background: var(--primary-color); flex-direction:column;
    width:220px; max-height:0; overflow:hidden;
    border-radius:0 0 10px 10px;
    box-shadow:0 8px 16px rgba(0,0,0,0.3);
    transition:max-height 0.35s ease;
    z-index:1500;
}
nav.open { max-height:350px; }
nav a {
    display:block; padding:12px 25px;
    color: var(--secondary-color); font-weight:600;
    font-size:1.1rem; text-decoration:none;
    border-radius:0 0 0 10px; transition: background 0.3s;
}
nav a:hover, nav a:focus { background: var(--secondary-color); color: var(--primary-color); outline:none; }

/* Menu Toggle */
.menu-toggle { display:flex; flex-direction:column; cursor:pointer; width:30px; height:25px; justify-content:space-between; user-select:none; z-index:1600; }
.menu-toggle span { height:3px; width:100%; background: var(--secondary-color); border-radius:3px; transition:all 0.3s; }
.menu-toggle.active span:nth-child(1) { transform: rotate(45deg) translate(5px,5px); }
.menu-toggle.active span:nth-child(2) { opacity:0; }
.menu-toggle.active span:nth-child(3) { transform: rotate(-45deg) translate(6px,-6px); }

/* Hero */
.hero {
    height:70vh; width:100%; max-width:1200px; border-radius:15px;
    display:flex; align-items:center; justify-content:center;
    text-align:center; padding:0 20px; flex-direction:column;
    color:#fff; text-shadow:1px 1px 6px rgba(0,0,0,0.5);
    margin:30px 0;
    background: linear-gradient(120deg, var(--accent-color) 0%, var(--primary-color) 100%);
}
.hero h1 { font-size:3rem; margin-bottom:20px; font-weight:900; letter-spacing:1.5px; }
.hero p { font-size:1.3rem; max-width:600px; margin-bottom:30px; font-weight:500; }

/* Buttons */
.btn-primary {
    background: var(--secondary-color); color: #0d1b3a; padding:12px 26px;
    font-size:1rem; font-weight:600; border:none; border-radius:8px;
    cursor:pointer; text-decoration:none; transition:all 0.3s; box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.btn-primary:hover, .btn-primary:focus { background:#ffb300; box-shadow:0 6px 18px rgba(0,0,0,0.25); }

/* Main & Features */
main {
    background: var(--feature-bg); width:100%; max-width:1200px;
    border-radius:15px; padding:40px 30px; box-shadow:0 4px 20px rgba(0,0,0,0.15);
    flex-grow:1; margin-bottom:30px;
}
.features { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:30px; }
.feature-box { background: var(--feature-bg); border-radius:12px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s; }
.feature-box:hover, .feature-box:focus-within { transform:translateY(-8px); box-shadow:0 10px 20px rgba(0,0,0,0.15); }
.feature-box h3 { color: var(--primary-color); margin-bottom:15px; font-weight:700; }
.feature-box p { color: var(--text-color); font-weight:500; font-size:1rem; }

/* Footer */
footer { background: var(--primary-color); color: var(--secondary-color); text-align:center;
         padding:20px 10px; font-size:0.9rem; border-radius:0 0 15px 15px; width:100%; max-width:1200px; }

/* Search bar */
.search-container { max-width:600px; margin:20px auto 40px; position:relative; }
#searchInput {
    width:100%; padding:14px 22px; font-size:1.1rem;
    border-radius:12px; border:2px solid var(--primary-color); outline:none;
    box-shadow:0 0 10px rgba(25,118,210,0.5); transition: border-color 0.3s, box-shadow 0.3s;
}
#searchInput:focus { border-color: var(--secondary-color); box-shadow:0 0 14px var(--secondary-color); }
#searchResults { position:absolute; top:56px; width:100%; background: var(--feature-bg);
    border:2px solid var(--primary-color); border-top:none; max-height:300px; overflow-y:auto;
    border-radius:0 0 12px 12px; box-shadow:0 6px 16px rgba(0,0,0,0.15); z-index:2000; }
#searchResults div.result-item { padding:12px 18px; cursor:pointer; border-bottom:1px solid #eee; font-weight:600; color: var(--primary-color); transition: background 0.25s, color 0.25s; }
#searchResults div.result-item:hover, #searchResults div.result-item[aria-selected="true"] { background: var(--secondary-color); color: var(--primary-color); outline:none; }

#personDetails { max-width:600px; margin:20px auto; background: var(--feature-bg); padding:25px; border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,0.1); display:none; }
#personDetails img { float:left; width:130px; height:130px; border-radius:50%; object-fit:cover; margin-right:25px; border:4px solid var(--primary-color); box-shadow:0 0 10px var(--primary-color); }
#personDetails h2 { margin-bottom:12px; color: var(--primary-color); font-family:'Segoe UI Black', sans-serif; font-size:2rem; }
#personDetails p { font-size:1.1rem; color: var(--text-color); line-height:1.6; font-weight:600; }
#personDetails::after { content:""; display:table; clear:both; }

/* Responsive nav */
@media(max-width:768px){ nav a{ font-size:1.2rem; } }
@media(max-width:600px){ header{ padding:15px 20px;} .hero{margin:20px 0;height:50vh;} .hero h1{ font-size:2.2rem;} .hero p{ font-size:1.1rem;} }

</style>
</head>
<body>

<header>
    <div class="logo" aria-label="Ukoo wa Makomelelo">Ukoo wa Makomelelo</div>
    <div style="display:flex; gap:10px; align-items:center;">
        <button id="darkModeToggle" style="padding:6px 10px; border-radius:8px; border:none; background:var(--secondary-color); color:#0d1b3a; cursor:pointer;">Dark Mode</button>
        <div class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0"><span></span><span></span><span></span></div>
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

<div class="search-container" role="search" aria-label="Tafuta mtu kwa jina">
    <input type="text" id="searchInput" placeholder="Tafuta mtu kwa jina..." autocomplete="off" aria-autocomplete="list" aria-controls="searchResults" aria-haspopup="listbox" aria-expanded="false"/>
    <div id="searchResults" role="listbox" tabindex="-1" aria-label="Matokeo ya utaftaji"></div>
</div>

<div id="personDetails" aria-live="polite" aria-atomic="true"></div>

<main>
    <section class="features" aria-label="Sehemu za huduma kuu">
        <div class="feature-box" tabindex="0"><h3>Usajili Rahisi</h3><p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p></div>
        <div class="feature-box" tabindex="0"><h3>Uchunguzi wa Familia</h3><p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p></div>
        <div class="feature-box" tabindex="0"><h3>Usalama wa Taarifa</h3><p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p></div>
        <div class="feature-box" tabindex="0"><h3>Muonekano wa Kisasa</h3><p>Tovuti yetu ni responsive na ina muonekano mzuri kwenye simu, kompyuta, na tablet.</p></div>
    </section>
</main>

<footer>&copy; 2025 Ukoo wa Makomelelo | Haki zote zimehifadhiwa</footer>

<script>
// Menu toggle
const menuToggle = document.getElementById('menu-toggle');
const navMenu = document.getElementById('nav-menu');
menuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('open');
    menuToggle.classList.toggle('active');
    navMenu.setAttribute('aria-hidden', navMenu.classList.contains('open') ? 'false':'true');
});
menuToggle.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' '){ e.preventDefault(); menuToggle.click(); }});

// Dark mode
const darkBtn = document.getElementById('darkModeToggle');
darkBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    darkBtn.textContent = document.body.classList.contains('dark') ? 'Light Mode':'Dark Mode';
    localStorage.setItem('darkMode', document.body.classList.contains('dark'));
});
if(localStorage.getItem('darkMode')==='true'){ document.body.classList.add('dark'); darkBtn.textContent='Light Mode'; }

// Search code remains same
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const personDetails = document.getElementById('personDetails');
let results=[],selectedIndex=-1;

function clearResults(){ searchResults.innerHTML=''; searchResults.setAttribute('aria-expanded','false'); selectedIndex=-1; searchInput.removeAttribute('aria-activedescendant'); }
function highlightResult(index){ const items=searchResults.querySelectorAll('.result-item'); items.forEach((item,i)=>{ if(i===index){ item.setAttribute('aria-selected','true'); item.scrollIntoView({block:'nearest'}); searchInput.setAttribute('aria-activedescendant',item.id); } else { item.removeAttribute('aria-selected'); }}); }
function showPersonDetails(person){ personDetails.style.display='block'; personDetails.innerHTML=`<img src="${person.photo_url?person.photo_url:'default-avatar.png'}" alt="Picha ya ${person.full_name}" /><h2>${person.full_name}</h2><p><strong>Umri:</strong> ${person.age||'Haijulikani'}</p><p><strong>Mkoa:</strong> ${person.region||'Haijulikani'}</p><p><strong>Kijiji/Mji:</strong> ${person.village||'Haijulikani'}</p><p><strong>Simu:</strong> ${person.phone||'Haijulikani'}</p><p><strong>Barua pepe:</strong> ${person.email||'Haijulikani'}</p><p><strong>Hali ya ndoa:</strong> ${person.marital_status||'Haijulikani'}</p><p><strong>Watoto:</strong> ${person.children||'Haijulikani'}</p>`; }

searchInput.addEventListener('input',()=>{
    const query=searchInput.value.trim();
    personDetails.style.display='none';
    clearResults();
    if(query.length<2) return;
    fetch('search.php?q='+encodeURIComponent(query))
        .then(res=>res.json())
        .then(data=>{ results=data; if(results.length===0){ searchResults.innerHTML='<div class="result-item" role="option">Hakuna mtu aliye patikana</div>'; searchResults.setAttribute('aria-expanded','true'); return; } searchResults.innerHTML=''; results.forEach((person,i)=>{ const div=document.createElement('div'); div.classList.add('result-item'); div.id='result-'+i; div.setAttribute('role','option'); div.textContent=person.full_name; div.dataset.index=i; div.tabIndex=-1; div.addEventListener('click',()=>{ showPersonDetails(person); clearResults(); searchInput.value=person.full_name; searchInput.focus(); }); searchResults.appendChild(div); }); searchResults.setAttribute('aria-expanded','true'); selectedIndex=-1; })
        .catch(()=>{ searchResults.innerHTML='<div class="result-item" role="option">Tatizo la mtandao. Jaribu tena.</div>'; searchResults.setAttribute('aria-expanded','true'); });
});

searchInput.addEventListener('keydown',e=>{
    const items=searchResults.querySelectorAll('.result-item'); if(items.length===0) return;
    if(e.key==='ArrowDown'){ e.preventDefault(); selectedIndex=(selectedIndex+1)%items.length; highlightResult(selectedIndex); }
    else if(e.key==='ArrowUp'){ e.preventDefault(); selectedIndex=(selectedIndex-1+items.length)%items.length; highlightResult(selectedIndex); }
    else if(e.key==='Enter'){ e.preventDefault(); if(selectedIndex>=0 && selectedIndex<results.length){ showPersonDetails(results[selectedIndex]); clearResults(); } }
    else if(e.key==='Escape'){ clearResults(); }
});
document.addEventListener('click',e=>{ if(!searchResults.contains(e.target)&&e.target!==searchInput){ clearResults(); }});
</script>

</body>
</html>
