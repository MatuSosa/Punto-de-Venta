<?php
include "../conexion.php";

$query = mysqli_query($conexion, "SELECT * FROM producto WHERE cantidad <=3");
$result = mysqli_num_rows($query);

$data = [];
if ($result > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
