<?php
include 'config.php';
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM family_tree WHERE id = $id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $photo = $row['photo'] ? "uploads/".$row['photo'] : "https://via.placeholder.com/150";
    echo "<div class='text-center'>
            <img src='{$photo}' class='img-fluid rounded-circle mb-3' width='150'>
            <h5>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</h5>
            <p><strong>Tarehe ya Kuzaliwa:</strong> {$row['dob']}</p>
            <p><strong>Jinsia:</strong> {$row['gender']}</p>
            <p><strong>Hali ya Ndoa:</strong> {$row['marital_status']}</p>
            <p><strong>Simu:</strong> {$row['phone']}</p>
            <p><strong>Email:</strong> {$row['email']}</p>
            <p><strong>Nchi:</strong> {$row['country']}</p>
            <p><strong>Mkoa:</strong> {$row['region']}</p>
            <p><strong>Wilaya:</strong> {$row['district']}</p>
          </div>";
} else {
    echo "<p>Hakuna taarifa zilizopatikana.</p>";
}
?>
