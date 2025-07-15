<?php
include 'conexion.php';

$organizacion_id = $_SESSION['organizacion_id'] ?? null;

header('Content-Type: application/json');

if (!$organizacion_id) {
    echo json_encode(['success' => false, 'message' => 'Organización no válida.']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de la factura no especificado.']);
    exit();
}

$id = intval($_GET['id']);

// Verificar que la factura pertenece a la organización activa
$stmt = $conn->prepare("SELECT id FROM facturas WHERE id = ? AND organizacion_id = ?");
$stmt->bind_param("ii", $id, $organizacion_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta factura o no existe.']);
    exit();
}

// Eliminar factura
$delete = $conn->prepare("DELETE FROM facturas WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la factura.']);
}
?>
