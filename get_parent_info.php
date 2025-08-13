<?php
include 'config.php';
$parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
if($parent_id<=0){ echo json_encode(['error'=>'ID sio sahihi']); exit; }

$res = pg_query_params($conn, "SELECT first_name,last_name FROM family_tree WHERE id=$1", [$parent_id]);
if(!$res || pg_num_rows($res)==0){ echo json_encode(['error'=>'Hakuna mzazi aliye na ID hiyo']); exit; }
$row = pg_fetch_assoc($res);
$parent_name = $row['first_name'].' '.$row['last_name'];

$res_max = pg_query_params($conn, "SELECT id FROM family_tree WHERE parent_id=$1 ORDER BY id DESC LIMIT 1", [$parent_id]);
if($res_max && pg_num_rows($res_max)>0){
    $row_max = pg_fetch_assoc($res_max);
    $last_child_id = (int)$row_max['id'];
    $parent_digits = (string)$parent_id;
    $last_digits = substr($last_child_id, strlen($parent_digits));
    $next_digit = (int)$last_digits + 1;
    if($next_digit > 999){ echo json_encode(['error'=>'Mzazi tayari ana watoto 999']); exit; }
    $next_child_id = (int)($parent_digits . str_pad($next_digit, strlen($last_digits), '0', STR_PAD_LEFT));
} else { $next_child_id = (int)($parent_id.'1'); }

echo json_encode(['name'=>$parent_name,'next_child_id'=>$next_child_id]);
