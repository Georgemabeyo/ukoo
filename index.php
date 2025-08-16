<?php
// index.php
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo | Karibu</title>

<!-- Early theme load (kuepuka mruko wa rangi wakati wa kupakia) -->
<script>
(function(){
  try{
    const t = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if(t==='dark' || (!t && prefersDark)){ document.documentElement.classList.add('dark'); }
    const accent = localStorage.getItem('accent');
    if(accent){ document.documentElement.style.setProperty('--accent', accent); }
  }catch(e){}
})();
</script>

<style>
/* ================== Design Tokens ================== */
:root{
  --bg: #F7FAFC;
  --surface: #FFFFFF;
  --text: #1F2937;     /* slate-800 */
  --muted: #64748B;    /* slate-500/600 */
  --primary: #1F3A5F;  /* deep navy */
  --primary-600: #264873;
  --accent: #14B8A6;   /* teal 500 */
  --accent-600: #0EA5A3;
  --ring: rgba(20,184,166,0.45);
}

.dark:root{
  --bg: #0B1020;       /* very dark blue */
  --surface: #0F172A;  /* slate-900-ish */
  --text: #E5E7EB;     /* gray-200 */
  --muted: #94A3B8;    /* gray-400/500 */
  --primary: #1E2B4A;  /* navy for dark */
  --primary-600: #26375D;
  /* --accent inabaki ila unaweza kubadilisha kupitia settings */
  --ring: rgba(20,184,166,0.35);
}

/* =============== Global Base Styles =============== */
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background: var(--bg);
  color: var(--text);
  line-height:1.6;
  display:flex; flex-direction:column; align-items:center;
  padding:0 16px 64px; /* nafasi chini kwa ajili ya toggle ya theme */
  transition: background .3s, color .3s;
}

/* =================== Header =================== */
header{
  width:100%;
  max-width:1200px;
  background: var(--primary);
  color:#fff;
  border-radius:0 0 16px 16px;
  padding:14px 20px;
  position:sticky; top:0; z-index:1000;
  display:flex; align-items:center; justify-content:space-between;
}

.logo{ font-weight:800; letter-spacing:1px; font-size:1.35rem; user-select:none; }

/* Menu toggle (hamburger) */
.menu-toggle{ display:none; width:32px; height:24px; cursor:pointer; position:relative; }
.menu-toggle span{
  position:absolute; left:0; right:0; height:3px; background:#fff; border-radius:3px; transition:transform .25s, opacity .25s, top .25s;
}
.menu-toggle span:nth-child(1){ top:0 }
.menu-toggle span:nth-child(2){ top:10px }
.menu-toggle span:nth-child(3){ top:20px }
.menu-toggle.active span:nth-child(1){ top:10px; transform:rotate(45deg) }
.menu-toggle.active span:nth-child(2){ opacity:0 }
.menu-toggle.active span:nth-child(3){ top:10px; transform:rotate(-45deg) }

/* =================== Nav =================== */
nav{
  position:absolute; top:100%; right:20px;
  width:240px;
  border-radius:12px;
  /* Kuzuia “block” in hanging when closed */
  padding:0;                 /* closed: no padding */
  background: transparent;   /* closed: transparent */
  box-shadow:none;           /* closed: no shadow */
  overflow:hidden;           /* keep content hidden */
  opacity:0; transform: translateY(-10px);
  pointer-events:none;
  transition: opacity .25s ease, transform .25s ease, padding .2s ease;
}
nav.open{
  background: var(--primary);
  padding:12px 0;            /* open: show padding */
  opacity:1; transform: translateY(0);
  pointer-events:auto;
  box-shadow: 0 12px 24px rgba(0,0,0,.25);
}

nav a{
  display:block; padding:12px 18px;
  color:#fff; text-decoration:none; font-weight:600; border-radius:8px;
  transition: background-color .2s, color .2s;
}
nav a:hover, nav a:focus{ background: rgba(255,255,255,.12); }

/* desktop nav */
.nav-inline{ display:flex; gap:18px; }
.nav-inline a{ padding:8px 10px; border-radius:6px; }
.nav-inline a:hover{ background: rgba(255,255,255,.14); }

/* show hamburger on mobile */
@media (max-width: 900px){
  .nav-inline{ display:none; }
  .menu-toggle{ display:block; }
}

/* =================== Hero =================== */
.hero{
  width:100%; max-width:1200px; margin:24px 0;
  border-radius:16px; overflow:hidden;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-600) 35%, #2C4E80 100%);
  color:#fff; text-align:center;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  padding:56px 18px;
}
.hero h1{ font-size: clamp(1.8rem, 3.8vw, 3rem); margin:8px 0 12px; letter-spacing:.5px; font-weight:900; }
.hero p{ max-width:720px; font-size: clamp(1rem, 2.5vw, 1.2rem); opacity:.95; margin-bottom:24px; }

.btn-primary{
  background: var(--accent);
  color:#05202A;
  padding:12px 22px; border:none; border-radius:10px; cursor:pointer; font-weight:700; text-decoration:none;
  box-shadow: 0 6px 18px rgba(0,0,0,.15);
  transition: transform .12s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
}
.btn-primary:focus, .btn-primary:hover{
  transform: translateY(-1px);
  box-shadow: 0 10px 24px rgba(0,0,0,.18);
  background: var(--accent-600); color:#fff;
}

/* =================== Main & Features =================== */
main{
  width:100%; max-width:1200px; background: var(--surface); color:var(--text);
  border-radius:16px; padding:32px 22px; box-shadow: 0 8px 24px rgba(0,0,0,.08);
  transition: background .3s, color .3s;
}
.grid{ display:grid; gap:22px; grid-template-columns: repeat(12, 1fr); }
.feature-box{
  grid-column: span 6; /* 2 per row on desktop */
  background: linear-gradient(180deg, rgba(255,255,255,.8), rgba(255,255,255,.9));
  border:1px solid rgba(2,6,23,.06);
  border-radius:14px; padding:22px;
  box-shadow: 0 4px 16px rgba(0,0,0,.06);
  transition: transform .2s ease, box-shadow .2s ease, background .3s;
}
.feature-box:hover{ transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,.10); }
.feature-box h3{ margin:0 0 8px; color: var(--primary); font-size:1.15rem; }
.feature-box p{ margin:0; color: var(--muted); font-weight:600; }

@media (max-width: 900px){
  .feature-box{ grid-column: span 12; } /* 1 per row on mobile */
}

/* =================== Search =================== */
.search-container{ max-width:700px; margin: 18px auto 32px; position:relative; }
#searchInput{
  width:100%; padding:14px 18px; font-size:1rem; border-radius:12px; outline:none;
  border:2px solid transparent; background: var(--surface); color: var(--text);
  box-shadow: 0 0 0 2px rgba(0,0,0,.04);
  transition: border-color .2s, box-shadow .2s, background .3s, color .3s;
}
#searchInput:focus{
  border-color: var(--accent);
  box-shadow: 0 0 0 4px var(--ring);
}
#searchResults{
  position:absolute; top:56px; width:100%; background: var(--surface);
  border:1px solid rgba(2,6,23,.08); border-top:none;
  max-height:320px; overflow-y:auto; border-radius:0 0 12px 12px;
  box-shadow: 0 12px 28px rgba(0,0,0,.12); z-index: 10;
  display:none; /* hidden default */
}
#searchResults.open{ display:block; }
.result-item{
  padding:12px 16px; cursor:pointer; border-bottom:1px solid rgba(2,6,23,.06);
  font-weight:600; color: var(--primary);
}
.result-item:hover, .result-item[aria-selected="true"]{
  background: var(--accent); color:#fff;
}

#personDetails{
  max-width:700px; margin: 12px auto 8px;
  background: var(--surface); color: var(--text);
  padding:18px; border-radius:12px;
  box-shadow: 0 6px 18px rgba(0,0,0,.08); display:none;
}
#personDetails img{
  float:left; width:120px; height:120px; object-fit:cover; border-radius:50%;
  margin-right:18px; border:4px solid var(--accent); box-shadow:0 0 0 4px var(--ring);
}
#personDetails h2{ margin:0 0 6px; color: var(--primary); }
#personDetails p{ margin:2px 0; font-weight:600; color: var(--text); }
#personDetails::after{ content:""; display:table; clear:both; }

/* =================== Footer =================== */
footer{
  width:100%; max-width:1200px; background: var(--primary); color:#fff;
  text-align:center; padding:16px; border-radius:0 0 16px 16px; margin-top:24px;
}

/* =================== Theme Toggle (fixed bottom center) =================== */
.theme-toggle{
  position:fixed; left:50%; bottom:16px; transform:translateX(-50%);
  z-index:1200;
  background: var(--accent); color:#05202A;
  border:none; border-radius:24px; padding:10px 16px; font-weight:700; cursor:pointer;
  box-shadow: 0 8px 24px rgba(0,0,0,.18);
  transition: background .2s, color .2s, transform .1s;
}
.theme-toggle:hover{ background: var(--accent-600); color:#fff; }
.theme-toggle:active{ transform: translateX(-50%) scale(.98); }

/* =================== Settings FAB & Panel =================== */
.settings-fab{
  position:fixed; right:16px; bottom:16px; z-index:1200;
  width:46px; height:46px; border-radius:50%; border:none; cursor:pointer;
  background: var(--primary); color:#fff; font-size:20px;
  box-shadow: 0 8px 24px rgba(0,0,0,.22);
}
.settings-panel{
  position:fixed; right:16px; bottom:72px; z-index:1200;
  width: min(92vw, 340px);
  background: var(--surface); color: var(--text);
  border-radius:14px; padding:14px 14px 12px;
  box-shadow: 0 18px 40px rgba(0,0,0,.25);
  border:1px solid rgba(2,6,23,.06);
  transform: translateY(16px); opacity:0; pointer-events:none;
  transition: transform .2s, opacity .2s, background .3s, color .3s;
}
.settings-panel.open{ transform: translateY(0); opacity:1; pointer-events:auto; }
.settings-panel h4{ margin:4px 4px 10px; font-size:1rem; color: var(--primary); }
.setting-row{ display:flex; align-items:center; justify-content:space-between; padding:8px 6px; gap:12px; }
.setting-row label{ font-weight:700; color: var(--muted); }
.setting-row .sw{ display:inline-flex; align-items:center; gap:8px; }

.select{
  width: 160px; padding:8px 10px; border-radius:10px; border:1px solid rgba(2,6,23,.12);
  background: var(--surface); color: var(--text);
}

/* Accessibility focus */
a, button, input, select{ outline:none }
a:focus, button:focus, input:focus, select:focus{ box-shadow: 0 0 0 3px var(--ring); border-radius:8px; }

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce){
  *, *::before, *::after{ transition: none !important; animation: none !important; }
}
</style>
</head>
<body>

<header>
  <div class="logo" aria-label="Ukoo wa Makomelelo">Ukoo wa Makomelelo</div>

  <!-- Desktop inline nav -->
  <div class="nav-inline" aria-label="Menyu kuu (desktop)">
    <a href="index.php">Nyumbani</a>
    <a href="registration.php">Jisajiri</a>
    <a href="family_tree.php">Ukoo</a>
    <a href="events.html">Matukio</a>
    <a href="contact.php">Mawasiliano</a>
  </div>

  <!-- Mobile toggle -->
  <div class="menu-toggle" id="menu-toggle" aria-label="Fungua/Funga menyu ya simu" role="button" tabindex="0">
    <span></span><span></span><span></span>
  </div>

  <!-- Mobile dropdown (no hanging box when closed) -->
  <nav id="nav-menu" role="navigation" aria-label="Main navigation" aria-hidden="true">
    <a href="index.php">Nyumbani</a>
    <a href="registration.php">Jisajiri</a>
    <a href="family_tree.php">Ukoo</a>
    <a href="events.html">Matukio</a>
    <a href="contact.php">Mawasiliano</a>
  </nav>
</header>

<section class="hero" role="banner" aria-label="Sehemu ya Karibu">
  <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
  <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
  <a href="registration.php" class="btn-primary" role="button" aria-label="Jiandikishe Sasa">Jiandikishe Sasa</a>
</section>

<main>
  <!-- Utaftaji -->
  <div class="search-container" role="search" aria-label="Tafuta mtu kwa jina">
    <input
      type="text"
      id="searchInput"
      placeholder="Tafuta mtu kwa jina..."
      autocomplete="off"
      aria-autocomplete="list"
      aria-controls="searchResults"
      aria-haspopup="listbox"
      aria-expanded="false"
    />
    <div id="searchResults" role="listbox" tabindex="-1" aria-label="Matokeo ya utafutaji"></div>
  </div>

  <div id="personDetails" aria-live="polite" aria-atomic="true"></div>

  <!-- Vipengele -->
  <section class="grid" aria-label="Sehemu za huduma kuu">
    <article class="feature-box" tabindex="0">
      <h3>Usajili Rahisi</h3>
      <p>Jaza taarifa zako, pakia picha, na ungana moja kwa moja na ukoo.</p>
    </article>
    <article class="feature-box" tabindex="0">
      <h3>Uchunguzi wa Familia</h3>
      <p>Angalia uhusiano wa ukoo, taarifa za ndugu na vizazi kwa urahisi.</p>
    </article>
    <article class="feature-box" tabindex="0">
      <h3>Usalama wa Taarifa</h3>
      <p>Taarifa zako zinalindwa kwa viwango vya juu vya usalama na faragha.</p>
    </article>
    <article class="feature-box" tabindex="0">
      <h3>Muonekano wa Kisasa</h3>
      <p>Tovuti ni responsive—inafanya kazi vyema kwenye simu, kompyuta, na tablet.</p>
    </article>
  </section>
</main>

<footer>
  &copy; <?= date('Y'); ?> Ukoo wa Makomelelo | Haki zote zimehifadhiwa
</footer>

<!-- Theme toggle (fixed bottom center) -->
<button class="theme-toggle" id="themeToggle" aria-label="Badili mandhari">🌙 Dark Mode</button>

<!-- Settings FAB -->
<button class="settings-fab" id="settingsFab" aria-label="Mipangilio">⚙️</button>

<!-- Settings Panel -->
<div class="settings-panel" id="settingsPanel" role="dialog" aria-label="Mipangilio ya Tovuti" aria-modal="false">
  <h4>Mipangilio</h4>
  <div class="setting-row">
    <label>Mandhari</label>
    <div class="sw">
      <button id="btnLight" class="btn-primary" style="padding:8px 12px;">Light</button>
      <button id="btnDark"  class="btn-primary" style="padding:8px 12px;">Dark</button>
    </div>
  </div>
  <div class="setting-row">
    <label>Rangi ya Accent</label>
    <select id="accentSelect" class="select" aria-label="Chagua rangi ya accent">
      <option value="#14B8A6">Teal (default)</option>
      <option value="#3B82F6">Blue</option>
      <option value="#22C55E">Green</option>
      <option value="#A855F7">Violet</option>
      <option value="#F43F5E">Rose</option>
    </select>
  </div>
</div>

<script>
// ================= Nav (bila hanging box) =================
const menuToggle = document.getElementById('menu-toggle');
const navMenu = document.getElementById('nav-menu');

function closeNav(){
  navMenu.classList.remove('open');
  navMenu.setAttribute('aria-hidden','true');
  menuToggle.classList.remove('active');
}
function openNav(){
  navMenu.classList.add('open');
  navMenu.setAttribute('aria-hidden','false');
  menuToggle.classList.add('active');
}

menuToggle.addEventListener('click', ()=>{
  if(navMenu.classList.contains('open')) closeNav(); else openNav();
});
menuToggle.addEventListener('keydown', (e)=>{
  if(e.key==='Enter'||e.key===' '){ e.preventDefault(); menuToggle.click(); }
});
// close when clicking outside
document.addEventListener('click', (e)=>{
  if(!navMenu.contains(e.target) && !menuToggle.contains(e.target)) closeNav();
});

// ================= Theme handling (global via localStorage) =================
const themeToggle = document.getElementById('themeToggle');
function setTheme(mode){ // 'light' or 'dark'
  if(mode==='dark'){
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme','dark');
    themeToggle.textContent = '☀️ Light Mode';
  }else{
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme','light');
    themeToggle.textContent = '🌙 Dark Mode';
  }
}
themeToggle.addEventListener('click', ()=>{
  const dark = document.documentElement.classList.contains('dark');
  setTheme(dark ? 'light' : 'dark');
});
// initial label
if(document.documentElement.classList.contains('dark')){
  themeToggle.textContent = '☀️ Light Mode';
}

// ================= Settings panel =================
const settingsFab = document.getElementById('settingsFab');
const settingsPanel = document.getElementById('settingsPanel');
settingsFab.addEventListener('click', ()=>{
  settingsPanel.classList.toggle('open');
});
document.addEventListener('click', (e)=>{
  if(!settingsPanel.contains(e.target) && !settingsFab.contains(e.target)){
    settingsPanel.classList.remove('open');
  }
});
// Settings buttons
document.getElementById('btnLight').addEventListener('click', ()=>setTheme('light'));
document.getElementById('btnDark').addEventListener('click', ()=>setTheme('dark'));

// Accent color
const accentSelect = document.getElementById('accentSelect');
function setAccent(val){
  document.documentElement.style.setProperty('--accent', val);
  // kidogo kivuli kwa hover pia
  try{
    const c = val;
    localStorage.setItem('accent', c);
  }catch(e){}
}
// set selected from storage if exists
(function(){
  const saved = localStorage.getItem('accent');
  if(saved){
    accentSelect.value = saved;
    setAccent(saved);
  }
})();
accentSelect.addEventListener('change', (e)=> setAccent(e.target.value));

// ================= Search (DB via search.php) =================
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const personDetails = document.getElementById('personDetails');

let results = [];
let selectedIndex = -1;

function clearResults(){
  searchResults.innerHTML = '';
  searchResults.classList.remove('open');
  searchInput.removeAttribute('aria-activedescendant');
  searchInput.setAttribute('aria-expanded','false');
  selectedIndex = -1;
}
function openResults(){
  searchResults.classList.add('open');
  searchInput.setAttribute('aria-expanded','true');
}
function highlightResult(idx){
  const items = searchResults.querySelectorAll('.result-item');
  items.forEach((el,i)=>{
    if(i===idx){
      el.setAttribute('aria-selected','true');
      el.scrollIntoView({block:'nearest'});
      searchInput.setAttribute('aria-activedescendant', el.id);
    }else{
      el.removeAttribute('aria-selected');
    }
  });
}
function showPersonDetails(person){
  personDetails.style.display='block';
  personDetails.innerHTML = `
    <img src="${person.photo_url ? person.photo_url : 'default-avatar.png'}" alt="Picha ya ${person.full_name}" />
    <h2>${person.full_name}</h2>
    <p><strong>Umri:</strong> ${person.age ?? 'Haijulikani'}</p>
    <p><strong>Mkoa:</strong> ${person.region ?? 'Haijulikani'}</p>
    <p><strong>Kijiji/Mji:</strong> ${person.village ?? 'Haijulikani'}</p>
    <p><strong>Simu:</strong> ${person.phone ?? 'Haijulikani'}</p>
    <p><strong>Barua pepe:</strong> ${person.email ?? 'Haijulikani'}</p>
    <p><strong>Hali ya ndoa:</strong> ${person.marital_status ?? 'Haijulikani'}</p>
    <p><strong>Watoto:</strong> ${person.children ?? 'Haijulikani'}</p>
  `;
}

searchInput.addEventListener('input', ()=>{
  const q = searchInput.value.trim();
  personDetails.style.display='none';
  clearResults();
  if(q.length < 2) return;

  fetch('search.php?q=' + encodeURIComponent(q))
    .then(r => r.ok ? r.json() : Promise.reject())
    .then(data => {
      results = Array.isArray(data) ? data : [];
      if(results.length === 0){
        searchResults.innerHTML = '<div class="result-item" role="option">Hakuna mtu aliye patikana</div>';
        openResults();
        return;
      }
      let frag = document.createDocumentFragment();
      results.forEach((person, i)=>{
        const div = document.createElement('div');
        div.className = 'result-item';
        div.id = 'result-' + i;
        div.setAttribute('role','option');
        div.textContent = person.full_name;
        div.addEventListener('click', ()=>{
          showPersonDetails(person);
          clearResults();
          searchInput.value = person.full_name;
          searchInput.focus();
        });
        frag.appendChild(div);
      });
      searchResults.innerHTML = '';
      searchResults.appendChild(frag);
      openResults();
      selectedIndex = -1;
    })
    .catch(()=>{
      searchResults.innerHTML = '<div class="result-item">Tatizo la mtandao. Jaribu tena.</div>';
      openResults();
    });
});

searchInput.addEventListener('keydown', (e)=>{
  const items = searchResults.querySelectorAll('.result-item');
  if(items.length === 0) return;
  if(e.key==='ArrowDown'){
    e.preventDefault();
    selectedIndex = (selectedIndex + 1) % items.length;
    highlightResult(selectedIndex);
  }else if(e.key==='ArrowUp'){
    e.preventDefault();
    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
    highlightResult(selectedIndex);
  }else if(e.key==='Enter'){
    e.preventDefault();
    if(selectedIndex >= 0 && selectedIndex < results.length){
      showPersonDetails(results[selectedIndex]);
      clearResults();
    }
  }else if(e.key==='Escape'){
    clearResults();
  }
});
// funga matokeo ukibofya nje
document.addEventListener('click', (e)=>{
  if(!searchResults.contains(e.target) && e.target !== searchInput){
    clearResults();
  }
});
</script>
</body>
</html>
