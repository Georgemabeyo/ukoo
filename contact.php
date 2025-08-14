<?php
// Handle form submission
$messageSent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Simple validation
    if (!$name || !$email || !$subject || !$message) {
        $error = 'Tafadhali jaza fomu yote.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Tafadhali tumia anwani ya barua pepe sahihi.';
    } else {
        // Send email (configure as needed)
        $to = 'admin@ukoo.com';  // Badilisha na email ya admin halisi
        $headers = "From: $name <$email>\r\nReply-To: $email\r\n";
        $mailSubject = "Ujumbe kutoka kwa $name: $subject";
        $mailBody = "Ujumbe:\n$message\n\nKutoka: $name\nEmail: $email";

        if (mail($to, $mailSubject, $mailBody, $headers)) {
            $messageSent = true;
        } else {
            $error = 'Tatizo la kutuma ujumbe. Jaribu tena baadaye.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ukoo wa Makomelelo | Contact</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #74ebd5 0%, #9face6 100%);
            color: #333;
            line-height: 1.6;
        }
        header {
            background: #0d47a1;
            color: #ffc107;
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
        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        @media(max-width: 768px) {
            nav {
                position: absolute;
                top: 60px;
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
                max-height: 300px;
            }
            nav a {
                padding: 12px 20px;
                font-size: 1.1rem;
            }
            .menu-toggle {
                display: flex;
            }
        }

        main {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0d47a1;
            margin-bottom: 25px;
            text-align: center;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            margin-top: 15px;
            color: #0d47a1;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #0d47a1;
            border-radius: 8px;
            font-size: 1rem;
            resize: vertical;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            border-color: #ffc107;
            outline: none;
        }
        textarea {
            min-height: 120px;
        }
        button {
            margin-top: 25px;
            width: 100%;
            background: #ffc107;
            color: #0d47a1;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 14px 0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background: #e6b007;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
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
            main {
                margin: 20px 10px;
                padding: 20px 25px;
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
        <a href="index.php">Nyumbani</a>
        <a href="registration.php">Jisajiri</a>
        <a href="family_tree.php">Ukoo</a>
        <a href="events.html">Matukio</a>
    </nav>
</header>

<main>
    <h1>Wasiliana Nasi</h1>

    <?php if ($messageSent): ?>
        <div class="message success">Ujumbe wako umetumwa kwa mafanikio! Tutawasiliana nawe hivi karibuni.</div>
    <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="contact.php" novalidate>
        <label for="name">Jina Lako</label>
        <input type="text" id="name" name="name" required placeholder="Jina lako kamili" />

        <label for="email">Barua Pepe</label>
        <input type="email" id="email" name="email" required placeholder="barua pepe yako" />

        <label for="subject">Mada ya Ujumbe</label>
        <input type="text" id="subject" name="subject" required placeholder="Mada" />

        <label for="message">Ujumbe Wako</label>
        <textarea id="message" name="message" required placeholder="Andika ujumbe wako hapa..."></textarea>

        <button type="submit">Tuma Ujumbe</button>
    </form>
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

    // Accessibility: Toggle menu with keyboard keys Enter or Space
    menuToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            menuToggle.click();
        }
    });
</script>

</body>
</html>
