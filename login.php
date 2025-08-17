<?php
session_start();
include 'config.php'; // faili la kuunganisha DB

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $msg = "Tafadhali jaza jina la mtumiaji na nenosiri.";
    } else {
        // Tafuta mtumiaji DB kwa username
        $sql = "SELECT id, username, password_hash, role FROM users WHERE username = $1 LIMIT 1";
        $result = pg_query_params($conn, $sql, [$username]);

        if ($result && pg_num_rows($result) == 1) {
            $user = pg_fetch_assoc($result);

            // Kagua password dhidi ya hash (hakikisha unahifadhi password hashed DB)
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                // Redirect to family tree or homepage
                header('Location: index.php');
                exit();
            } else {
                $msg = "Jina la mtumiaji au nenosiri si sahihi.";
            }
        } else {
            $msg = "Jina la mtumiaji au nenosiri si sahihi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ingia | Ukoo wa Makomelelo</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
    <div class="container" style="max-width: 400px; margin: 50px auto;">
        <h2>Ingia kwenye Mfumo wa Ukoo wa Makomelelo</h2>
        <?php if ($msg): ?>
            <p style="color: red;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Jina la Mtumiaji</label>
            <input type="text" name="username" id="username" required />
            <label for="password">Nenosiri</label>
            <input type="password" name="password" id="password" required />
            <button type="submit" class="btn-primary" style="margin-top: 15px;">Ingia</button>
        </form>
        <p>Huna akaunti? <a href="registration.php">Jiandikishe hapa</a>.</p>
    </div>
</body>
</html>
