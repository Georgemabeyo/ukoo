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
        transition: max-height 0.3s ease;
        background: transparent;
        position: static;
        max-height: none;
        box-shadow: none;
    }
    nav::-webkit-scrollbar {
        display: none;
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
    .menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
        width: 34px;
        height: 28px;
        justify-content: space-between;
        user-select: none;
        z-index: 99999;
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

    /* Search Styles */
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
    #searchResults div.result-item:hover,
    #searchResults div.result-item[aria-selected="true"] {
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

    @media(max-width: 600px) {
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
    <div class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0" aria-expanded="false" aria-controls="nav-menu">
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
        aria-describedby="searchHelp"
    />
    <div id="searchResults" role="listbox" tabindex="-1" aria-label="Matokeo ya utaftaji"></div>
</div>

<div id="personDetails" aria-live="polite" aria-atomic="true"></div>

<script>
    // Nav toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    menuToggle.addEventListener('click', () => {
        const isOpen = navMenu.classList.toggle('open');
        menuToggle.classList.toggle('active');
        menuToggle.setAttribute('aria-expanded', isOpen);
    });

    menuToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            menuToggle.click();
        }
    });

    // Demo data to simulate search results (replace this with fetch from server in your real app)
    const peopleData = [
        {id:1, full_name:"Yakobo Makomelelo", village:"Katete", ward:"Kibaha", region:"Pwani", photo_url:"https://randomuser.me/api/portraits/men/75.jpg"},
        {id:2, full_name:"Maryam Makomelelo", village:"Katete", ward:"Kibaha", region:"Pwani", photo_url:"https://randomuser.me/api/portraits/women/65.jpg"},
        {id:3, full_name:"Joseph Makomelelo", village:"Katete", ward:"Kibaha", region:"Pwani", photo_url:"https://randomuser.me/api/portraits/men/66.jpg"},
        {id:4, full_name:"Fatuma Makomelelo", village:"Katete", ward:"Kibaha", region:"Pwani", photo_url:"https://randomuser.me/api/portraits/women/77.jpg"},
        {id:5, full_name:"David Makomelelo", village:"Katete", ward:"Kibaha", region:"Pwani", photo_url:"https://randomuser.me/api/portraits/men/54.jpg"},
    ];

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const personDetails = document.getElementById('personDetails');

    let currentFocus = -1;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        personDetails.style.display = 'none';
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        searchInput.setAttribute('aria-expanded', 'false');
        currentFocus = -1;

        if (query.length < 2) return;

        // Filter demo data by name match (simulate server search)
        const filtered = peopleData.filter(p => p.full_name.toLowerCase().includes(query));

        if(filtered.length === 0) {
            searchResults.innerHTML = `<div class="result-item" role="option" aria-selected="false">Hakuna mtu aliye patikana</div>`;
            searchResults.style.display = 'block';
            searchInput.setAttribute('aria-expanded', 'true');
            return;
        }

        filtered.forEach(person => {
            const div = document.createElement('div');
            div.classList.add('result-item');
            div.textContent = person.full_name;
            div.dataset.id = person.id;
            div.setAttribute('role', 'option');
            div.setAttribute('tabindex', '-1');
            div.setAttribute('aria-selected', 'false');

            div.addEventListener('click', () => {
                showPersonDetails(person);
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
                searchInput.value = person.full_name;
                searchInput.setAttribute('aria-expanded', 'false');
            });

            div.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    div.click();
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if(div.nextSibling) div.nextSibling.focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if(div.previousSibling) div.previousSibling.focus();
                    else searchInput.focus();
                }
            });

            searchResults.appendChild(div);
        });

        searchResults.style.display = 'block';
        searchInput.setAttribute('aria-expanded', 'true');
    });

    searchInput.addEventListener('keydown', (e) => {
        const items = searchResults.querySelectorAll('.result-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            if (currentFocus >= items.length) currentFocus = 0;
            items.forEach(item => item.setAttribute('aria-selected', 'false'));
            items[currentFocus].setAttribute('aria-selected', 'true');
            items[currentFocus].focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            if (currentFocus < 0) currentFocus = items.length - 1;
            items.forEach(item => item.setAttribute('aria-selected', 'false'));
            items[currentFocus].setAttribute('aria-selected', 'true');
            items[currentFocus].focus();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if(currentFocus > -1) {
                items[currentFocus].click();
            }
        } else if (e.key === 'Escape') {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            searchInput.setAttribute('aria-expanded', 'false');
        }
    });

    function showPersonDetails(person) {
        personDetails.style.display = 'block';
        personDetails.innerHTML = `
            <img src="${person.photo_url || 'https://via.placeholder.com/130?text=No+Image'}" alt="Picha ya ${person.full_name}" />
            <h2>${person.full_name}</h2>
            <p><strong>Kijiji:</strong> ${person.village || 'Haijulikani'}</p>
            <p><strong>Kata:</strong> ${person.ward || 'Haijulikani'}</p>
            <p><strong>Mkoa:</strong> ${person.region || 'Haijulikani'}</p>
        `;
    }

    // Close results if clicking outside search container
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            searchInput.setAttribute('aria-expanded', 'false');
        }
    });
</script>

</body>
</html>
