<?php
include 'conexion.php';

$organizacion_id = $_SESSION['organizacion_id'];
if (!isset($_GET['id'])) {
    die("❌ ID del producto no especificado.");
}

$id = intval($_GET['id']);

// Verificar que el producto pertenece a la organización activa
$stmt = $conn->prepare("SELECT id FROM productos WHERE id = ? AND organizacion_id = ?");
$stmt->bind_param("ii", $id, $organizacion_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("❌ No tienes permiso para eliminar este producto o no existe.");
}

// (Opcional) Verificar si el producto está en uso en alguna factura
$check = $conn->prepare("SELECT id FROM factura_items WHERE producto_id = ?");
$check->bind_param("i", $id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    die("❌ No puedes eliminar este producto porque está vinculado a una o más facturas.");
}

// Eliminar producto
$delete = $conn->prepare("DELETE FROM productos WHERE id = ?");
$delete->bind_param("i", $id);
if ($delete->execute()) {
    header("Location: productos.php");
    exit();
} else {
    echo "❌ Error al eliminar el producto.";
}
?>
