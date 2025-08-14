<?php
include 'config.php';

$level = $_GET['level'] ?? '';
header('Content-Type: text/html; charset=UTF-8');

if($level=='region'){
    $res = pg_query($conn, "SELECT DISTINCT region FROM locations ORDER BY region");
    while($row = pg_fetch_assoc($res)){
        echo '<option value="'.htmlspecialchars($row['region']).'">'.htmlspecialchars($row['region']).'</option>';
    }
} elseif($level=='district' && isset($_GET['region'])){
    $region = $_GET['region'];
    $res = pg_query_params($conn, "SELECT DISTINCT district FROM locations WHERE region=$1 ORDER BY district", [$region]);
    while($row = pg_fetch_assoc($res)){
        echo '<option value="'.htmlspecialchars($row['district']).'">'.htmlspecialchars($row['district']).'</option>';
    }
} elseif($level=='ward' && isset($_GET['district'])){
    $district = $_GET['district'];
    $res = pg_query_params($conn, "SELECT DISTINCT ward FROM locations WHERE district=$1 ORDER BY ward", [$district]);
    while($row = pg_fetch_assoc($res)){
        echo '<option value="'.htmlspecialchars($row['ward']).'">'.htmlspecialchars($row['ward']).'</option>';
    }
} elseif($level=='village' && isset($_GET['ward'])){
    $ward = $_GET['ward'];
    $res = pg_query_params($conn, "SELECT DISTINCT street FROM locations WHERE ward=$1 ORDER BY street", [$ward]);
    while($row = pg_fetch_assoc($res)){
        echo '<option value="'.htmlspecialchars($row['street']).'">'.htmlspecialchars($row['street']).'</option>';
    }
}
?>
