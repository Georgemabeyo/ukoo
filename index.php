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
        nav a:hover {
            background: #ffc107;
            color: #0d47a1;
        }
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
        <nav>
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
</body>
</html>
