<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $res = pg_query_params($conn, "DELETE FROM persons WHERE id = $1", [$id]);
    if ($res) {
        header("Location: list.php?msg=Mtu amefutwa kwa mafanikio");
        exit;
    } else {
        echo "Tatizo la kufuta: " . pg_last_error($conn);
    }
} else {
    echo "ID batili.";
}
