<?php
$messageSent = false;
$error = '';

$isLoggedIn = isset($_SESSION['user_id']);
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
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ukoo wa Makomelelo | Mawasiliano</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">

<?php include 'header.php'; ?>

<main class="container">
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
    <button type="submit" class="btn-primary">Tuma Ujumbe</button>
</form>
</main>

<?php include 'footer.php'; ?>

<script src="scripts.js"></script>
</body>
</html>
