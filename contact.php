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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0 20px;
            color: #333;
        }
        header {
            background: #0d47a1;
            color: #ffc107;
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
        nav a:hover,
        nav a:focus {
            background: #ffc107;
            color: #0d47a1;
            outline: none;
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
    <nav>
        <a href="index.php">Home</a>
        <a href="registration.php">Registration</a>
        <a href="family_tree.php">Family Tree</a>
        <a href="contact.php">Contact</a>
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
</body>
</html>
