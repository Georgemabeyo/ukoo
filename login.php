<?php
session_start();
include 'config.php'; // Unganisha DB connection $conn

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $msg = "Tafadhali jaza jina la mtumiaji na nenosiri.";
    } else {
        // Tafuta mtumiaji DB kwa username hasa kama string, pasipo kuita strtolower au kubadilisha
        $sql = "SELECT id, username, password, FROM family_tree WHERE username = $1 LIMIT 1";
        $result = pg_query_params($conn, $sql, [$username]);

        if ($result && pg_num_rows($result) == 1) {
            $user = pg_fetch_assoc($result);

            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="light-mode">
    <div class="container" style="max-width: 400px; margin: 50px auto;">
        <h2>Ingia kwenye Mfumo wa Ukoo wa Makomelelo</h2>
        <?php if ($msg): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form method="post" action="login.php" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Jina la Mtumiaji</label>
                <input type="text" name="username" id="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nenosiri</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ingia</button>
        </form>
        <p class="mt-3">Huna akaunti? <a href="registration.php">Jiandikishe hapa</a>.</p>
    </div>
</body>
</html>
