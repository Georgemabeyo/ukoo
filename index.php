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
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        color: #222;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    header {
        background: #0d47a1;
        color: #ffc107;
        padding: 18px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        position: relative;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 10;
        overflow-x: visible;
    }
    header .logo {
        font-weight: 900;
        font-size: 2.2rem;
        letter-spacing: 3px;
        user-select: none;
        font-family: 'Segoe UI Black', sans-serif;
        white-space: nowrap;
    }
    nav {
        display: flex;
        gap: 28px;
        flex-wrap: nowrap;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        padding-left: 10px;
        padding-right: 10px;
        min-width: 0;
    }
    nav::-webkit-scrollbar {
        display: none; /* Chrome, Safari */
    }
    nav a {
        color: #ffc107;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 10px 18px;
        border-radius: 8px;
        background: rgba(255 193 7 / 0.1);
        transition: background-color 0.3s ease, color 0.3s ease;
        user-select: none;
        white-space: nowrap;
        box-shadow: 0 0 6px rgba(255, 193, 7, 0.4);
        flex-shrink: 0;
    }
    nav a:hover,
    nav a:focus {
        background: #ffc107;
        color: #0d47a1;
        outline: none;
        box-shadow: 0 0 10px #ffc107;
    }

    /* Ongeza Contact link ionekane */
    nav a[href="contact.php"] {
        display: inline-block;
    }

    /* Hamburger menu button */
    .menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
        width: 34px;
        height: 28px;
        justify-content: space-between;
        user-select: none;
    }
    .menu-toggle span {
        height: 4px;
        width: 100%;
        background: #ffc107;
        border-radius: 3px;
        transition: all 0.3s ease;
        box-shadow: 0 0 4px #ffc107;
    }
    .menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }
    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }
    .menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(7px, -7px);
    }

    /* Responsive Styles */
    @media(max-width: 768px) {
        nav {
            position: absolute;
            top: 100%;
            right: 0;
            background: #0d47a1;
            flex-direction: column;
            width: 240px;
            padding: 20px 0;
            border-radius: 0 0 0 15px;
            max-height: 0;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            transition: max-height 0.3s ease;
            z-index: 9999;
        }
        nav.open {
            max-height: 400px;
        }
        nav a {
            padding: 15px 25px;
            font-size: 1.25rem;
            box-shadow: none;
            background: none;
            color: #ffc107;
            border-radius: 0;
        }
        nav a:hover,
        nav a:focus {
            background: #ffc107;
            color: #0d47a1;
            box-shadow: none;
        }
        .menu-toggle {
            display: flex;
        }
    }

    /* Hero */
    .hero {
        background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center/cover;
        height: 75vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 25px;
        color: white;
        text-shadow: 2px 2px 12px rgba(0,0,0,0.8);
        flex-direction: column;
        user-select: none;
    }
    .hero h1 {
        font-size: 3.8rem;
        margin-bottom: 24px;
        font-weight: 900;
        text-transform: none;  /* no uppercase */
        letter-spacing: 2px;
        font-family: 'Segoe UI Black', sans-serif;
        text-shadow: 3px 3px 18px rgba(0,0,0,0.85);
    }
    .hero p {
        font-size: 1.6rem;
        max-width: 650px;
        margin-bottom: 40px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .btn-primary {
        background: #ffc107;
        color: #0d47a1;
        padding: 18px 42px;
        font-size: 1.3rem;
        font-weight: 900;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 6px 15px rgba(255, 193, 7, 0.7);
        transition: background-color 0.35s ease, box-shadow 0.35s ease;
        user-select: none;
    }
    .btn-primary:hover,
    .btn-primary:focus {
        background: #e6b007;
        box-shadow: 0 8px 20px rgba(230, 176, 7, 0.9);
        outline: none;
    }

    /* Main content */
    main {
        padding: 50px 25px;
        max-width: 1200px;
        margin: auto;
        flex-grow: 1;
    }
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(320px,1fr));
        gap: 40px;
    }
    .feature-box {
        background: white;
        border-radius: 18px;
        padding: 35px 30px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
        transition: transform 0.35s ease, box-shadow 0.35s ease;
        cursor: default;
        user-select: none;
    }
    .feature-box:hover {
        transform: translateY(-15px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    .feature-box h3 {
        color: #0d47a1;
        margin-bottom: 22px;
        font-weight: 900;
        font-size: 1.9rem;
        letter-spacing: 0.04em;
        font-family: 'Segoe UI Black', sans-serif;
    }
    .feature-box p {
        color: #555;
        font-weight: 600;
        font-size: 1.1rem;
        line-height: 1.5;
    }

    /* Footer */
    footer {
        background: #0d47a1;
        color: #ffc107;
        text-align: center;
        padding: 24px 15px;
        font-size: 1rem;
        font-weight: 600;
        user-select: none;
        box-shadow: inset 0 3px 8px rgba(0,0,0,0.2);
    }

    /* Search bar */
    .search-container {
        max-width: 600px;
        margin: 30px auto 50px;
        position: relative;
    }
    #searchInput {
        width: 100%;
        padding: 16px 25px;
        font-size: 1.15rem;
        border-radius: 12px;
        border: 2.5px solid #0d47a1;
        outline: none;
        box-shadow: 0 0 12px rgba(13,71,161,0.5);
        transition: border-color 0.3s ease;
        font-weight: 600;
        user-select: text;
    }
    #searchInput:focus {
        border-color: #ffc107;
        box-shadow: 0 0 16px #ffc107;
    }
    #searchResults {
        position: absolute;
        top: 58px;
        width: 100%;
        background: white;
        border: 2px solid #0d47a1;
        border-top: none;
        max-height: 280px;
        overflow-y: auto;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        z-index: 10000;
        user-select: none;
    }
    #searchResults div.result-item {
        padding: 14px 20px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-weight: 600;
        font-size: 1rem;
        color: #0d47a1;
        transition: background-color 0.25s ease, color 0.25s ease;
    }
    #searchResults div.result-item:hover {
        background: #ffc107;
        color: #0d47a1;
    }
    #personDetails {
        max-width: 600px;
        margin: 35px auto 0;
        background: white;
        padding: 30px 25px;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(0,0,0,0.12);
        display: none;
        user-select: none;
    }
    #personDetails img {
        float: left;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 25px;
        border: 4px solid #0d47a1;
        box-shadow: 0 0 10px #0d47a1;
    }
    #personDetails h2 {
        margin-bottom: 14px;
        color: #0d47a1;
        font-family: 'Segoe UI Black', sans-serif;
        font-size: 2rem;
    }
    #personDetails p {
        font-size: 1.1rem;
        color: #222;
        line-height: 1.5;
        font-weight: 600;
    }
    #personDetails::after {
        content: "";
        display: table;
        clear: both;
    }

    /* Responsive typography and layout tweaks */
    @media(max-width: 600px) {
        .hero h1 {
            font-size: 2.6rem;
        }
        .hero p {
            font-size: 1.25rem;
            max-width: 90vw;
        }
        header {
            padding: 15px 20px;
            justify-content: space-between;
        }
        nav {
            gap: 18px;
        }
        main {
            padding: 35px 15px;
        }
        .feature-box {
            padding: 25px 20px;
        }
        #personDetails {
            padding: 25px 15px;
            margin: 25px 15px 0;
        }
        #personDetails img {
            width: 100px;
            height: 100px;
            margin-right: 15px;
        }
        #personDetails h2 {
            font-size: 1.6rem;
        }
    }
</style>
</head>
<body>
<header>
    <div class="logo" aria-label="Ukoo wa Makomelelo">Ukoo wa Makomelelo</div>
    <div class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <nav id="nav-menu" role="navigation" aria-label="Main navigation">
        <a href="index.php">Nyumbani</a>
        <a href="registration.php">Jisajiri</a>
        <a href="family_tree.php">Wanaukoo</a>
        <a href="events.html">Matukio</a>
        <a href="contact.php">Mawasiliano</a>
    </nav>
</header>

<section class="hero" role="banner" aria-label="Hero Section">
    <h1>Karibu kwenye mfumo wa Ukoo wa Makomelelo</h1>
    <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
    <a href="registration.php" class="btn-primary" role="button" aria-label="Jiandikishe sasa">Jiandikishe Sasa</a>
</section>

<div class="search-container" role="search" aria-label="Tafuta mtu kwa jina">
    <input type="text" id="searchInput" placeholder="Tafuta mtu kwa jina..." autocomplete="off" aria-autocomplete="list" aria-controls="searchResults" aria-haspopup="listbox" />
    <div id="searchResults" role="listbox" tabindex="-1"></div>
</div>

<div id="personDetails" aria-live="polite" aria-atomic="true"></div>

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
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('open');
        menuToggle.classList.toggle('active');
    });

    menuToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            menuToggle.click();
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const personDetails = document.getElementById('personDetails');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();
        personDetails.style.display = 'none';
        searchResults.innerHTML = '';
        if (query.length < 2) return;

        fetch('search.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="result-item" role="option" aria-selected="false">Hakuna mtu aliye patikana</div>';
                    return;
                }
                data.forEach(person => {
                    const div = document.createElement('div');
                    div.classList.add('result-item');
                    div.textContent = person.full_name;
                    div.dataset.id = person.id;
                    div.setAttribute('role', 'option');
                    div.setAttribute('tabindex', '0');
                    div.addEventListener('click', () => {
                        showPersonDetails(person);
                        searchResults.innerHTML = '';
                        searchInput.value = person.full_name;
                    });
                    div.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            div.click();
                        }
                    });
                    searchResults.appendChild(div);
                });
            })
            .catch(() => {
                searchResults.innerHTML = '<div class="result-item" role="option" aria-selected="false">Tatizo la mtandao. Jaribu tena.</div>';
            });
    });

    function showPersonDetails(person) {
        personDetails.style.display = 'block';
        personDetails.innerHTML = `
            <img src="${person.photo_url || 'https://via.placeholder.com/130?text=No+Image'}" alt="Picha ya ${person.full_name}" />
            <h2>${person.full_name}</h2>
            <p><strong>Umri:</strong> ${person.age || 'Haijulikani'}</p>
            <p><strong>Mkoa:</strong> ${person.region || 'Haijulikani'}</p>
            <p><strong>Maelezo:</strong> ${person.description || 'Hakuna maelezo zaidi.'}</p>
        `;
    }
</script>
</body>
</html>
