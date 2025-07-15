<?php
header('Content-Type: application/json');

include 'conexion.php';

// Ejecutar la consulta
$sql = "SELECT COUNT(*) AS total FROM clientes";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Devolver el total
echo json_encode(["total_clientes" => $row["total"]]);
$conn->close();
?>
