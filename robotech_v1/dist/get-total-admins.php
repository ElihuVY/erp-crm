<?php
header('Content-Type: application/json');

include 'conexion.php';

$sql = "SELECT COUNT(*) AS total FROM usuarios";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(["total_admins" => $row["total"]]);
$conn->close();
?>
