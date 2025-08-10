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
            background: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        header {
            background: #0d47a1; /* deep blue */
            color: #ffc107; /* amber yellow */
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: relative;
        }
        header .logo {
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 2px;
            cursor: default;
        }
        nav {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }
        nav a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        nav a:hover,
        nav a:focus {
            background: #ffc107;
            color: #0d47a1;
            outline: none;
        }

        /* Hamburger menu button */
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            width: 30px;
            height: 25px;
            justify-content: space-between;
        }
        .menu-toggle span {
            height: 3px;
            width: 100%;
            background: #ffc107;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        /* Toggle active animation */
        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Responsive Styles */
        @media(max-width: 768px) {
            nav {
                position: absolute;
                top: 100%;
                right: 0;
                background: #0d47a1;
                flex-direction: column;
                width: 220px;
                padding: 15px 0;
                border-radius: 0 0 0 10px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                z-index: 1000;
            }
            nav.open {
                max-height: 300px; /* enough to show all links */
            }
            nav a {
                padding: 12px 20px;
                font-size: 1.1rem;
            }
            .menu-toggle {
                display: flex;
            }
        }

        /* Hero and rest unchanged */
        .hero {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center/cover;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            color: white;
            text-shadow: 1px 1px 8px rgba(0,0,0,0.7);
            flex-direction: column;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 900;
        }
        .hero p {
            font-size: 1.3rem;
            max-width: 600px;
            margin-bottom: 30px;
            font-weight: 500;
        }
        .btn-primary {
            background: #ffc107;
            color: #0d47a1;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background: #e6b007;
        }

        main {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 30px;
        }
        .feature-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-box:hover {
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
        footer {
            background: #0d47a1;
            color: #ffc107;
            text-align: center;
            padding: 20px 10px;
            margin-top: 50px;
            font-size: 0.9rem;
            user-select: none;
        }

        /* Search bar styles */
        .search-container {
            max-width: 600px;
            margin: 20px auto 40px;
            position: relative;
        }
        #searchInput {
            width: 100%;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1.5px solid #0d47a1;
            outline: none;
            box-shadow: 0 0 5px rgba(13,71,161,0.4);
        }
        #searchResults {
            position: absolute;
            top: 48px;
            width: 100%;
            background: white;
            border: 1px solid #0d47a1;
            border-top: none;
            max-height: 250px;
            overflow-y: auto;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 10000;
        }
        #searchResults div.result-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        #searchResults div.result-item:hover {
            background: #ffc107;
            color: #0d47a1;
        }
        #personDetails {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: none;
        }
        #personDetails img {
            float: left;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 3px solid #0d47a1;
        }
        #personDetails h2 {
            margin-bottom: 8px;
            color: #0d47a1;
        }
        #personDetails p {
            font-size: 1rem;
            color: #333;
            line-height: 1.4;
        }
        #personDetails::after {
            content: "";
            display: table;
            clear: both;
        }

        @media(max-width:600px){
            .hero h1 {
                font-size: 2.2rem;
            }
            .hero p {
                font-size: 1.1rem;
            }
            header {
                justify-content: center;
                gap: 15px;
            }
            nav {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Ukoo wa Makomelelo</div>
        <div class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav id="nav-menu" role="navigation">
            <a href="index.php">Home</a>
            <a href="registration.php">Registration</a>
            <a href="family_tree.php">Family Tree</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Karibu kwenye Mfumo wa Ukoo wa Makomelelo</h1>
        <p>Ungana na familia yako, tushirikiane kujenga urithi wa familia kwa vizazi vijavyo.</p>
        <a href="registration.php" class="btn-primary">Jiandikishe Sasa</a>
    </section>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Tafuta mtu kwa jina..." autocomplete="off" />
        <div id="searchResults"></div>
    </div>

    <div id="personDetails"></div>

    <main>
        <section class="features">
            <div class="feature-box">
                <h3>Usajili Rahisi</h3>
                <p>Jaza taarifa zako kwa urahisi, upload picha, na ungana moja kwa moja na ukoo.</p>
            </div>
            <div class="feature-box">
                <h3>Uchunguzi wa Familia</h3>
                <p>Angalia uhusiano wa familia zako, talifa na watoto wa mfuasi wako kwa urahisi.</p>
            </div>
            <div class="feature-box">
                <h3>Usalama wa Taarifa</h3>
                <p>Taarifa zako zinahifadhiwa kwa usiri mkubwa na usalama wa hali ya juu.</p>
            </div>
            <div class="feature-box">
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

        // Toggle menu open/close on hamburger click
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('open');
            menuToggle.classList.toggle('active');
        });

        // Accessibility: Allow toggling menu via keyboard (Enter or Space)
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
            personDetails.style.display = 'none'; // hide details on new search
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }
            fetch('search.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="result-item">Hakuna mtu aliye patikana</div>';
                        return;
                    }
                    data.forEach(person => {
                        const div = document.createElement('div');
                        div.classList.add('result-item');
                        div.textContent = person.full_name;
                        div.dataset.id = person.id;
                        div.addEventListener('click', () => {
                            showPersonDetails(person);
                            searchResults.innerHTML = '';
                            searchInput.value = person.full_name;
                        });
                        searchResults.appendChild(div);
                    });
                })
                .catch(() => {
                    searchResults.innerHTML = '<div class="result-item">Tatizo la mtandao. Jaribu tena.</div>';
                });
        });

        function showPersonDetails(person) {
            personDetails.style.display = 'block';
            personDetails.innerHTML = `
                <img src="${person.photo ? person.photo : 'default-avatar.png'}" alt="Picha ya ${person.full_name}" />
                <h2>${person.full_name}</h2>
                <p><strong>Umri:</strong> ${person.age || 'Haijulikani'}</p>
                <p><strong>Mkoa:</strong> ${person.region || 'Haijulikani'}</p>
                <p><strong>Mji/Kijiji:</strong> ${person.town || 'Haijulikani'}</p>
                <p><strong>Simu:</strong> ${person.phone || 'Haijulikani'}</p>
                <p><strong>Barua pepe:</strong> ${person.email || 'Haijulikani'}</p>
                <p><strong>Hali ya ndoa:</strong> ${person.marital_status || 'Haijulikani'}</p>
                <p><strong>Watoto:</strong> ${person.children || 'Haijulikani'}</p>
            `;
        }
    </script>
</body>
</html>
