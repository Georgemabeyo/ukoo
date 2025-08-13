<?php
session_start();

// Cheki kama admin amelogin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Jina la admin (kutoka kwenye session)
$admin_name = $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #0d47a1;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background-color: #ffc107;
            padding: 10px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        nav a {
            color: #0d47a1;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
        }
        nav a:hover {
            background-color: #0d47a1;
            color: white;
        }
        .container {
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 15px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        footer {
            background-color: #0d47a1;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        @media (max-width: 600px) {
            nav {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Karibu, <?php echo htmlspecialchars($admin_name); ?></h1>
    <p>Admin Dashboard</p>
</header>

<nav>
    <a href="admin_members.php">Wanachama</a>
    <a href="admin_parents.php">Wazazi</a>
    <a href="admin_users.php">Watumiaji</a>
    <a href="logout.php">Toka</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Habari za mfumo</h2>
        <p>Hii ni dashboard yako ya admin. Kutoka hapa unaweza kudhibiti taarifa zote.</p>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> - Mfumo wa Usimamizi
</footer>

</body>
</html>
