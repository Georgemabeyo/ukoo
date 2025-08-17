<?php
$messageSent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Tafadhali jaza fomu yote.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Tafadhali tumia anwani ya barua pepe sahihi.';
    } else {
        $to = 'admin@ukoo.com';
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ukoo wa Makomelelo | Mawasiliano</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:'Segoe UI', sans-serif; transition: background 0.3s, color 0.3s; }
body.light-mode { background:#f0f4f8; color:#222; }
body.dark-mode { background:#1e293b; color:#f8fafc; }

header {
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 25px; background:#0d47a1; border-radius:0 0 12px 12px;
}
header .logo { font-size:1.8rem; font-weight:700; color:#ffc107; }

.nav-links { display:flex; gap:20px; align-items:center; }
.nav-links a { color:#ffc107; font-weight:600; text-decoration:none; transition:0.3s; }
.nav-links a:hover { color:#0d47a1; background:#ffc107; border-radius:5px; padding:5px 10px; }

.nav-toggle {
    display:none; flex-direction:column; justify-content:space-between;
    width:30px; height:25px; background:transparent; border:none; cursor:pointer;
}
.nav-toggle span {
    display:block; height:3px; background:#ffc107; border-radius:2px; transition:0.3s;
}
.nav-toggle.active span:nth-child(1){ transform:rotate(45deg) translate(5px,5px);}
.nav-toggle.active span:nth-child(2){ opacity:0;}
.nav-toggle.active span:nth-child(3){ transform:rotate(-45deg) translate(5px,-5px);}

/* Form */
main { max-width:600px; margin:40px auto; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); background:white; transition:background 0.3s, color 0.3s; }
body.dark-mode main { background:#334155; color:#f8fafc; }
h1 { text-align:center; margin-bottom:25px; color:#0d47a1; }
body.dark-mode h1 { color:#facc15; }
label { font-weight:600; margin-top:15px; display:block; }
input, textarea { width:100%; padding:12px 15px; margin-bottom:10px; border-radius:8px; border:2px solid #0d47a1; transition:0.3s; }
input:focus, textarea:focus { outline:none; border-color:#facc15; }
button { width:100%; padding:14px; font-weight:700; border-radius:8px; border:none; background:#ffc107; color:#0d47a1; transition:0.3s; }
button:hover { background:#e6b007; }

/* Messages */
.message { margin-top:20px; padding:15px; border-radius:8px; text-align:center; font-weight:600; }
.message.success { background:#d4edda; color:#155724; }
.message.error { background:#f8d7da; color:#721c24; }

/* Footer */
footer { text-align:center; padding:20px 10px; background:#0d47a1; color:#ffc107; margin-top:50px; border-radius:12px; }

/* Responsive */
@media(max-width:768px){
    .nav-links { flex-direction:column; position:absolute; top:60px; right:20px; background:#0d47a1; border-radius:10px; overflow:hidden; max-height:0; transition:max-height 0.35s ease; }
    .nav-links.show { max-height:300px; }
    .nav-toggle { display:flex; }
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
        <a href="events.html">Matukio</a>
        <span id="toggleTheme" style="cursor:pointer; font-weight:700;">Dark Mode</span>
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
    <input type="text" id="name" name="name" required placeholder="Jina lako kamili">

    <label for="email">Barua Pepe</label>
    <input type="email" id="email" name="email" required placeholder="barua pepe yako">

    <label for="subject">Mada ya Ujumbe</label>
    <input type="text" id="subject" name="subject" required placeholder="Mada">

    <label for="message">Ujumbe Wako</label>
    <textarea id="message" name="message" required placeholder="Andika ujumbe wako hapa..."></textarea>

    <button type="submit">Tuma Ujumbe</button>
</form>
</main>

<footer>
    &copy; 2025 Ukoo wa Makomelelo | Haki zote zimehifadhiwa
</footer>

<script>
// Navbar toggle
const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
navToggle.addEventListener('click', ()=>{
    navToggle.classList.toggle('active');
    navLinks.classList.toggle('show');
});

// Dark/Light mode toggle
const themeToggle = document.getElementById('toggleTheme');
themeToggle.addEventListener('click', ()=>{
    if(document.body.classList.contains('light-mode')){
        document.body.classList.replace('light-mode','dark-mode');
        themeToggle.textContent='Light Mode';
    } else {
        document.body.classList.replace('dark-mode','light-mode');
        themeToggle.textContent='Dark Mode';
    }
});
</script>
</body>
</html>
