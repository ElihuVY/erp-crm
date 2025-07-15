<?php
include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

if (isset($_GET['org_id'])) {
    $org_id = intval($_GET['org_id']);

    $sql = "SELECT id, nombre FROM productos 
            WHERE organizacion_id = $org_id 
            ORDER BY nombre ASC";
    $result = $conn->query($sql);

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($productos);
}
