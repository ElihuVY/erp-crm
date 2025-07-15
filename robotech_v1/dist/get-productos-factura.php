<?php
header('Content-Type: application/json');
include 'conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$productos = [];

if ($id > 0) {
    // Obtenemos los productos asociados a la factura
    $sql = "SELECT fi.id, fi.producto_id, fi.descripcion, fi.cantidad, fi.precio_unitario
            FROM factura_items fi
            WHERE fi.factura_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $productos[] = [
            'id' => $row['id'],
            'producto_id' => $row['producto_id'],
            'nombre' => $row['descripcion'], // Usamos la descripción como nombre
            'cantidad' => $row['cantidad'],
            'precio' => $row['precio_unitario']
        ];
    }
}

echo json_encode(['productos' => $productos]);