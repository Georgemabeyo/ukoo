<?php
include 'config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $ward = trim($_POST['ward']);

    if ($full_name === '') {
        $message = "Tafadhali jaza jina kamili.";
    } else {
        $sql = "INSERT INTO persons (full_name, ward) VALUES ($1, $2)";
        $result = pg_query_params($conn, $sql, [$full_name, $ward]);
        if ($result) {
            header("Location: list.php?msg=Imefanikiwa-kuongeza");
            exit;
        } else {
            $message = "Kosa limejitokeza: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <title>Ongeza Mtu Mpya</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="dark-mode">
    <h2>Ongeza Mtu Mpya</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Jina Kamili:</label><br />
        <input type="text" name="full_name" required /><br /><br />
        <label>Kata:</label><br />
        <input type="text" name="ward" /><br /><br />
        <button type="submit">Ongeza</button>
    </form>
    <p><a href="list.php">Rudi kwenye Orodha</a></p>
</body>
</html>
