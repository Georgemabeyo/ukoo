<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM family_tree WHERE id = $1";
    $del_result = pg_query_params($conn, $del_sql, [$delete_id]);

    if ($del_result) {
        $_SESSION['message'] = "Entry imefutwa kwa mafanikio.";
    } else {
        $_SESSION['message'] = "Tatizo katika kufuta entry: " . pg_last_error($conn);
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Prepare base query
if ($search_query !== '') {
    $sql = "SELECT * FROM family_tree WHERE LOWER(first_name) LIKE LOWER($1) OR LOWER(middle_name) LIKE LOWER($1) OR LOWER(last_name) LIKE LOWER($1) ORDER BY id DESC";
    $params = ['%' . $search_query . '%'];
} else {
    $sql = "SELECT * FROM family_tree ORDER BY id DESC";
    $params = [];
}

$result = pg_query_params($conn, $sql, $params);

?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - Ukoo wa Makomelelo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Dashboard ya Admin</h2>
        <a href="admin_logout.php" class="btn btn-danger">Toka</a>
    </div>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tafuta kwa jina..." value="<?= htmlspecialchars($search_query) ?>">
            <button class="btn btn-primary" type="submit">Tafuta</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Ongeza yote</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Jina Kamili</th>
                    <th>Tarehe ya Kuzaliwa</th>
                    <th>Jinsia</th>
                    <th>Hali ya Ndoa</th>
                    <th>Watoto Wanaume</th>
                    <th>Watoto Wanawake</th>
                    <th>Nchi</th>
                    <th>Mawasiliano</th>
                    <th>Parent ID</th>
                    <th>Picha</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && pg_num_rows($result) > 0): ?>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['dob']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['marital_status']) ?></td>
                        <td><?= intval($row['children_male']) ?></td>
                        <td><?= intval($row['children_female']) ?></td>
                        <td><?= htmlspecialchars($row['country']) ?></td>
                        <td>
                            Simu: <?= htmlspecialchars($row['phone']) ?><br>
                            Email: <?= htmlspecialchars($row['email']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['parent_id']) ?></td>
                        <td>
                            <?php if (!empty($row['photo'])): ?>
                                <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Picha" style="width:50px; height:auto;">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_family.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Badili</a>

                            <a href="admin_dashboard.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mb-1"
                               onclick="return confirm('Una uhakika unataka kufuta entry hii?');">Futa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="12" class="text-center">Hakuna data.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
